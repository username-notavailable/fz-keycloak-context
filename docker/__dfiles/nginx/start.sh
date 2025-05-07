#!/bin/bash

/sbin/cron &

/sbin/nginx -g 'daemon off;'