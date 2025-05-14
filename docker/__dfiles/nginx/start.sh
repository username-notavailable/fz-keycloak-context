#!/bin/bash

/init_dns.sh

rm /etc/nginx/conf.d/*.conf

/docker-entrypoint.sh nginx

/sbin/nginx -s quit
/sbin/nginx -g 'daemon off;'