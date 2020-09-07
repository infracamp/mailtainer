<?php
/**
 * Output the PGP encrypted 7z data from the /data repostitory
 *
 * Basic authentication is required as well.
 */


require __DIR__ . "/../src/bootstrap.php";
ini_set('display_errors', 1);

sleep(mt_rand(1,6));
if ( ! isset ($_SERVER["PHP_AUTH_PW"]) || ! password_verify($_SERVER["PHP_AUTH_PW"], BACKUP_AUTH_PASS_HASH)) {
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
    die ("Invalid auth token");
}


if ( ! file_exists(BACKUP_PGP_PUBLIC_KEY_FILE))
    throw new InvalidArgumentException("PGP public key not found. Backup disabled!");

set_time_limit(3600);

$output = phore_exec("gpg --homedir /tmp --import ?" ,[BACKUP_PGP_PUBLIC_KEY_FILE], true);
if ( ! preg_match("/\\<([a-z0-9.-_@]+)\\>/i", $output[0], $matches)) {
    throw new InvalidArgumentException("Cannot parse email from output");
}
$email = $matches[1];

header("Content-Type: application/binary");
passthru("sudo /bin/tar -czf - /data | gpg --homedir /tmp --always-trust -e -r '". escapeshellarg($email) . "'", $ret);
if ($ret !== 0)
    throw new Exception("Error running compressor");

phore_file("/tmp/last_successful_bku")->set_contents(date("Y-m-d H:i:s"));
