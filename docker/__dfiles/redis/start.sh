#!/bin/bash

/init_dns.sh

if [[ ! -f "/mnt/redis.conf" ]]
then
    cp /redis.conf /mnt/redis.conf
    chmod 666 /mnt/redis.conf
fi

/bin/envsubst < /mnt/redis.conf > /mnt/running.conf
/usr/local/bin/redis-server /mnt/running.conf