#!/bin/bash

docker rm -f promobit_mongodb_1 \
&& docker volume rm promobit_mongodata \
&& docker-compose -f docker-compose.mongodb.yml build \
&& docker-compose -f docker-compose.mongodb.yml up