#!/bin/bash

/init_dns.sh

if [[ ! -f "/mnt/memcached.conf" ]]
then
    cp /memcached.conf /mnt/memcached.conf
    chmod 666 /mnt/memcached.conf
fi

/bin/envsubst < /mnt/memcached.conf > /mnt/running.conf

CMDLINE="$(cat /mnt/running.conf | grep "^[^# ]" | tr '\n' ' ')"

/usr/local/bin/memcached $CMDLINE