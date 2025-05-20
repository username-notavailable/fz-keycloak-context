#!/bin/bash

/init_dns.sh

if [[ "$(ls -1 /etc/nginx/conf.d)" != "" ]]
then
    rm /etc/nginx/conf.d/*.conf
fi

/check_conf.sh &

/docker-entrypoint.sh nginx -g 'daemon off;'