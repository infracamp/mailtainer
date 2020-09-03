# Mailtainer Private Server

## TL;DR

- [Demo docker-stackfile.yml](doc/mailtainer-compose.yml)
- Preconfigured IMAP/POP3 Mailserver to be configured with
  one single yaml file.
- Out of the box support for Letsencrypt (SSL)
- Setup & ready to go in 60 seconds

## Deployment / Configuration

```bash
sudo apt-get install docker.io curl
sudo mkdir /mailtailer_data
sudo curl -o /mailtainer_data/mailtainer-cfg.yml https://raw.githubusercontent.com/infracamp/mailtainer/master/doc/mailtainer-cfg.yml
sudo curl -o /mailtainer_data/mailtainer-compose.yml https://raw.githubusercontent.com/infracamp/mailtainer/master/doc/mailtainer-compose.yml
## Adjust the files mailtainer-cfg.yml and mailtainer-compose.yml

sudo docker stack deploy -c /mailtainer_data/mailtainer-compose.yml mailtainer  
```

Generating hashed passwords:

```bash
mkpasswd -m SHA-512 <password>
```


## Mail-Client Settings

### Mozilla Thunderbird




0) Make sure the mail-domain matches your IP and port 80 is available
in you firewall configuration. (Letsencrypt will fail otherwise)

1) Starting mailtainer is quite simple. Just run:

```bash
docker volume create mailtainer-vol1
docker service create  \
            --mount mailtainer-vol1:/data  \
            --net=host  \
            -p 80:80 -p 25:25 -p 143:143 \
            -e MAILNAME=mail.fqdn \
            --hostname <mail.fqdn> \
            infracamp/mailtainer
```


3) 

## Mailserver

