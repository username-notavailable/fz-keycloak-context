FROM redis

RUN set -eux; \
apt-get update; \
apt-get install -y procps;

COPY redis.conf /usr/local/etc/redis/redis.conf
#COPY sysctl.conf /etc/sysctl.conf

#RUN /sbin/sysctl vm.overcommit_memory=1

EXPOSE 6379

ENTRYPOINT [ "redis-server", "/usr/local/etc/redis/redis.conf" ]
