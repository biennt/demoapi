#!/bin/bash

docker stop trafficgen js php nginxplus
docker rm trafficgen js php nginxplus
docker network rm demoapi

