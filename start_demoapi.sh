#!/bin/bash

docker network create --subnet 10.10.10.0/24 demoapi

docker run -d --name nginxplus \
-v ${PWD}/nginxplus/conf.d:/etc/nginx/conf.d \
-p 80:80 -p 443:443 \
--env=NGINX_LICENSE_JWT=$(cat ${PWD}/../license.jwt) --runtime=runc \
--network demoapi --ip 10.10.10.2 \
--restart unless-stopped nginxplus

docker run -d --name php \
-v ${PWD}/php:/var/www/html \
--network demoapi --ip 10.10.10.3 \
--restart unless-stopped php:apache


docker run -d --name js \
-v ${PWD}/jsonserver:/app \
--network demoapi --ip 10.10.10.4 \
--restart unless-stopped biennt/js

docker run -d --name trafficgen \
-p 8080:8080 \
--network demoapi --ip 10.10.10.5 \
--restart unless-stopped lexuansy/jwt-simulator:1.0

