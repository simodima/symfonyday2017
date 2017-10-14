#!/bin/bash
set -e
docker-compose stop
docker-compose rm -f
docker-compose up -d