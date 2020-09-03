# Mailtainer Private Server

## TL;DR

- [Demo docker-stackfile.yml](doc/mailtainer-compose.yml)
- [Demo account-config.yml](doc/mailtainer-cfg.yml)
- Preconfigured IMAP/POP3 Mailserver to be configured with
  one single yaml file.
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
| `ENABLE_LETSENCRYPT` | 1   | Enable automatic aquiring / renewing of SSL certificates |

### Keep your config in a repository





## Mail-Client Settings

### Mozilla Thunderbird

![Settings](doc/settings-thunderbird.png)






