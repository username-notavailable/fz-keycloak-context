FROM keycloak/keycloak:26.2.4 AS builder

# Enable health and metrics support
ENV KC_HEALTH_ENABLED=true
ENV KC_METRICS_ENABLED=true

# Configure a database vendor
ENV KC_DB=postgres

WORKDIR /opt/keycloak
# for demonstration purposes only, please make sure to use proper certificates in production instead
#RUN keytool -genkeypair -storepass password -storetype PKCS12 -keyalg RSA -keysize 2048 -dname "CN=server" -alias server -ext "SAN:c=DNS:localhost,IP:127.0.0.1" -keystore conf/server.keystore
RUN /opt/keycloak/bin/kc.sh build

FROM debian:stable

RUN set -eux; \
apt-get update; \
apt-get install -y apt-utils; \
apt-get install -y gettext-base; \
apt-get install -y nano; \
apt-get install -y net-tools; \
apt-get install -y iputils-ping; \
apt-get install -y iptables; \
apt-get install -y procps; \
apt-get install -y bind9-dnsutils; \
apt-get install -y letsencrypt; \
apt-get install -y cron; \
apt-get install -y jq; \
apt-get install -y default-jre-headless;

COPY --from=builder /opt/keycloak/ /opt/keycloak/
COPY start.sh /start.sh
COPY init_dns.sh /init_dns.sh

ENTRYPOINT []

CMD ["/start.sh"]
