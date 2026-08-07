# Checklist Mail config

- [ ] Ensure IP's PTR Record matches hostname/mailname
  > `host <ip-addr>` should be the same than `<hostname>` 
  
- [ ] Ensure SPF Records are correct
  > `@ IN TXT 180 v=spf1 mx ip4:<ip>/32 ip6:<ipv6>/128 -all` 

- [ ] Ensure DKIM Records are correct
  > The container prints the required public key on startup. Add it for each sender domain: `mail._domainkey.<domain> IN TXT "v=DKIM1; k=rsa; p=..."`

- [ ] Ensure DMARC Records are correct
  > `_dmarc IN TXT "v=DMARC1; p=none"` initially for testing. After SPF and DKIM are verified, change this to `p=reject`.

- [ ] Ensure MX Records are correct
