#!/bin/bash
###########

# https://stackoverflow.com/questions/64066263/docker-nginx-autoreload-on-config-file-changes

while true
do
    inotifywait --exclude .swp -e create -e modify -e delete -e move /etc/nginx/templates
    nginx -t

    if [ $? -eq 0 ]
    then
        echo "Detected nginx templates directory change"
        echo "Executing: nginx -s quit"
        nginx -s quit > /dev/null
    fi
done
