FROM redis

RUN set -eux; \
apt-get update; \
apt-get install -y apt-utils; \
apt-get install -y gettext-base; \
apt-get install -y telnet; \
apt-get install -y nano; \
apt-get install -y net-tools; \
apt-get install -y iputils-ping; \
apt-get install -y iptables; \
apt-get install -y procps; \
apt-get install -y bind9-dnsutils; \
apt-get install -y letsencrypt; \
apt-get install -y cron; \
apt-get install -y jq;

COPY start.sh /start.sh
COPY init_dns.sh /init_dns.sh
COPY redis.conf /redis.conf
#COPY sysctl.conf /etc/sysctl.conf

CMD ["/start.sh"]
