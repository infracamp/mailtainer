FROM nfra/kickstart-flavor-php:7.4

ENV DEV_CONTAINER_NAME="mailtainer"

ADD / /opt
RUN ["bash", "-c",  "chown -R user /opt"]
RUN ["/kickstart/run/entrypoint.sh", "build"]

ENTRYPOINT ["/kickstart/run/entrypoint.sh", "standalone"]
