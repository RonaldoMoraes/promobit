#!/bin/bash

docker rm -f promobit_mongodb
docker volume rm promobit_mongo_data
docker rm -f promobit_rabbitmq
docker volume rm promobit_rabbitmq_data
docker rm -f promobit_redis
docker-compose -f docker-compose.development.yml build
docker-compose -f docker-compose.development.yml up