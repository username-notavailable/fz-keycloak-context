#!/bin/bash

PID_FILE="/var/run/nginx.pid"

certbot renew

echo "EXIT_CODE = $?"

if [[ "$?" == 0 && -f "$PID_FILE" ]]
then
	echo "Reload nginx conf"
	kill -s HUP $(cat "$PID_FILE")
fi