# Checklist Mail config

- [ ] Ensure IP's PTR Record matches hostname/mailname
  > `host <ip-addr>` should be the same than `<hostname>` 
  
- [ ] Ensure SPF Records are correct
  > `@ IN TXT 180 v=spf1 mx ip4:<ip>/32 ip6:<ipv6>/128 -all` 

- [ ] Ensure MX Records are correct
