#!/bin/bash

/init_dns.sh

/bin/envsubst < /usr/local/etc/redis/redis.conf > /usr/local/etc/redis/running.conf
/usr/local/bin/redis-server /usr/local/etc/redis/running.conf