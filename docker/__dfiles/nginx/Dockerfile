FROM nginx:stable

RUN set -eux; \
apt-get update; \
apt-get upgrade -y; \
apt-get install -y apt-utils; \
apt-get install -y nano; \
apt-get install -y net-tools; \
apt-get install -y iputils-ping; \
apt-get install -y procps; \
apt-get install -y bind9-dnsutils; \
apt-get install -y letsencrypt; \
apt-get install -y cron; \
apt-get install -y jq;

COPY start.sh /start.sh
COPY update_cloudflare_dns.sh /update_cloudflare_dns.sh
COPY certbot_renew.sh /certbot_renew.sh

COPY crontab /etc/crontab

EXPOSE 80
EXPOSE 443

CMD ["/start.sh"]
