#!/usr/bin bash

echo -e "\e[36mBuilding new containers\e[0m"
docker-compose build

echo -e "\e[36mRemoving old containers\e[0m"
docker-compose down

echo -e "\e[36mDeleting volume\e[0m"
docker volume rm promobit_my-db

if [[ "-d" == "$1" ]]; then
  echo -e "\e[36mdocker-compose up -d\e[0m"
  docker-compose up -d
else
  echo -e "\e[36mdocker-compose up\e[0m"
  docker-compose up
fi