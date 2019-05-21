# Mailtainer Private Server



## TL;DR

1) Starting mailtainer is quite simple. Just run:

```bash
docker volume create mailtainer-vol1
docker servcie create  -e CONFIG_REPO=git@github.com:infracamp/mailtainer.git  \
            --mount source=mailtainer-vol1,target=/mnt  \
            --net=host  \
            -p 80:80
            infracamp/mailtainer
```

2) Download the ssh-public key `http://my.server.tld/.well-known/ssh-public-key` and
set it as project deploy-key (github) or Deploy-Key (gitlab) with read-only access.

3) 

## Mailserver

