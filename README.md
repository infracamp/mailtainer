# Mailtainer - All In One Mailserver (IMAP/SMTP)

A mailserver image build on Dovecot, Postfix, Amavis, ClamAV, Spamassassin, 
Letsencrypt. 

## TL;DR;

- Public available image - runs on docker or kubernetes
    - [Demo docker-stackfile.yml](doc/mailtainer-compose.yml)
- AllInOne Yaml Config File
    - [Demo account-config.yml](doc/mailtainer-cfg.yml)
- Out of the box support for Letsencrypt (SSL)
- Setup & ready to go in 60 seconds

## Deployment / Configuration



```bash
sudo apt-get install docker.io curl
sudo mkdir /mailtainer_data
sudo curl -o /mailtainer_data/mailtainer-cfg.yml https://raw.githubusercontent.com/infracamp/mailtainer/master/doc/mailtainer-cfg.yml
sudo curl -o /mailtainer_data/mailtainer-compose.yml https://raw.githubusercontent.com/infracamp/mailtainer/master/doc/mailtainer-compose.yml

## Adjust the files mailtainer-cfg.yml and mailtainer-compose.yml

sudo docker swam init
sudo docker stack deploy -c /mailtainer_data/mailtainer-compose.yml mailtainer  
```

Generating hashed passwords:

```bash
mkpasswd -m SHA-512 <password>
```

### Configuration

| Environment Name | Default | Description |
|------------------|-------------|---------|
| `MAILNAME`       | --          | The hostname this server is running on                           |
| `CONFIG_FILE`    | `/data/mailtainer-cfg.yml` | The path to the config file inside the container  |
| `RBL_CLIENT`     | `sbl-xbl.spamhaus.org;dnsbl.sorbs.net` | RBL hosts |
| `ENABLE_LETSENCRYPT` | 1   | Enable automatic acquiring / renewing of SSL certificates |

## Mail-Client Settings

### Mozilla Thunderbird

![Settings](doc/settings-thunderbird.png)



## Debugging

- [SMTP DNS Settings Checklist](doc/checklist-mail-config.md)


## Backup & Recovery

To backup, just copy `/data/dovecot` to an external server.


## Images

| Image                            | Description                                |
|----------------------------------|--------------------------------------------|
| `infracamp/mailtainer:1.0`       | Stable build. Recent updates               |
| `infracamp/mailtainer:1.0.x`     | Release build. Fixed version (no updates)  |
| `infracamp/mailtainer:1.0-dev`   | Development build. Testing only            |


