#!/bin/bash
set -e
docker-compose stop
docker-compose rm -f
docker-compose up -d 

waitforit -address=http://localhost:15672 -timeout=20 

docker exec -it symfonyday2017_php_1 \
    bin/console rabbitmq:setup-fabric