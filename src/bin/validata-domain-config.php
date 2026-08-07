#!/usr/bin/env php
<?php

require __DIR__ . "/../bootstrap.php";

use Infracamp\Mailtainer\Type\TConfig;

function fail(string $message): void
{
    fwrite(STDERR, "[DOMAIN-CONFIG] FAIL: $message\n");
}

function ok(string $message): void
{
    echo "[DOMAIN-CONFIG] OK: $message\n";
}

function getTxtRecords(string $name): array
{
    $records = @dns_get_record($name, DNS_TXT);
    if ($records === false) {
        return [];
    }

    $txt = [];
    foreach ($records as $record) {
        if (isset($record["txt"])) {
            $txt[] = $record["txt"];
            continue;
        }
        if (isset($record["entries"]) && is_array($record["entries"])) {
            $txt[] = implode("", $record["entries"]);
        }
    }
    return $txt;
}

function normalizeTxt(string $txt): string
{
    return preg_replace('/["\s]+/', '', $txt);
}

function publicKeyFromPrivateKey(string $file): string
{
    if (! is_readable($file)) {
        throw new RuntimeException("DKIM private key is not readable: $file");
    }

    $privateKey = openssl_pkey_get_private(file_get_contents($file));
    if ($privateKey === false) {
        throw new RuntimeException("Invalid DKIM private key: $file");
    }

    $details = openssl_pkey_get_details($privateKey);
    if ($details === false || empty($details["key"])) {
        throw new RuntimeException("Cannot extract DKIM public key from: $file");
    }

    return preg_replace('/-----BEGIN PUBLIC KEY-----|-----END PUBLIC KEY-----|\s+/', '', $details["key"]);
}

function validateSpf(string $domain, array &$errors): void
{
    $spf = [];
    foreach (getTxtRecords($domain) as $txt) {
        if (stripos(trim($txt), "v=spf1") === 0) {
            $spf[] = $txt;
        }
    }

    if (count($spf) === 0) {
        $errors[] = "$domain: missing SPF TXT record (expected one TXT record starting with 'v=spf1')";
        return;
    }
    if (count($spf) > 1) {
        $errors[] = "$domain: multiple SPF TXT records found; SPF requires exactly one";
        return;
    }

    ok("$domain SPF record found");
}

function validateDmarc(string $domain, array &$errors): void
{
    $name = "_dmarc.$domain";
    $dmarc = [];
    foreach (getTxtRecords($name) as $txt) {
        if (stripos(trim($txt), "v=DMARC1") === 0) {
            $dmarc[] = $txt;
        }
    }

    if (count($dmarc) === 0) {
        $errors[] = "$domain: missing DMARC TXT record at $name (example: 'v=DMARC1; p=none')";
        return;
    }
    if (count($dmarc) > 1) {
        $errors[] = "$domain: multiple DMARC TXT records found at $name; DMARC requires exactly one";
        return;
    }
    if (! preg_match('/(?:^|;)\s*p\s*=\s*(none|quarantine|reject)\s*(?:;|$)/i', $dmarc[0])) {
        $errors[] = "$domain: DMARC record at $name has no valid p=none|quarantine|reject policy";
        return;
    }

    ok("$domain DMARC record found");
}

function validateDkim(string $domain, string $selector, string $publicKey, array &$errors): void
{
    $name = "$selector._domainkey.$domain";
    $expected = "p=$publicKey";

    foreach (getTxtRecords($name) as $txt) {
        $normalized = normalizeTxt($txt);
        if (stripos($normalized, "v=DKIM1") !== false && strpos($normalized, $expected) !== false) {
            ok("$domain DKIM record found at $name");
            return;
        }
    }

    $errors[] = "$domain: DKIM TXT record at $name is missing or contains a different public key. Expected value: v=DKIM1; k=rsa; p=$publicKey";
}

try {
    $config = phore_hydrate(
        phore_file(CONFIG_FILE)->get_yaml(),
        TConfig::class
    );
} catch (Throwable $e) {
    fail("Cannot read config file " . CONFIG_FILE . ": " . $e->getMessage());
    exit(1);
}

if (! $config instanceof TConfig) {
    fail("Invalid config file: " . CONFIG_FILE);
    exit(1);
}

$domains = $config->getAllMailDomains();
if (count($domains) === 0) {
    fail("No mail domains found in config file: " . CONFIG_FILE);
    exit(1);
}

$errors = [];
$dkimPublicKey = null;
if (defined("ENABLE_DKIM") && ENABLE_DKIM) {
    try {
        $dkimPublicKey = publicKeyFromPrivateKey(DKIM_PRIVATE_KEY_FILE);
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

echo "[DOMAIN-CONFIG] Validating DNS records for: " . implode(", ", $domains) . "\n";

foreach ($domains as $domain) {
    validateSpf($domain, $errors);
    validateDmarc($domain, $errors);

    if (defined("ENABLE_DKIM") && ENABLE_DKIM && $dkimPublicKey !== null) {
        validateDkim($domain, DKIM_SELECTOR, $dkimPublicKey, $errors);
    }
}

if (count($errors) > 0) {
    fwrite(STDERR, "\n[DOMAIN-CONFIG] Domain configuration validation failed. Fix DNS or disable the failing feature before starting the container.\n");
    foreach ($errors as $error) {
        fail($error);
    }
    exit(1);
}

ok("all domain DNS checks passed");
