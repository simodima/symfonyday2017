#!/bin/bash
set -e

NUM_REQ=100

# Should terminate all calls at ctrl-c
while true 
do
    ab -n $NUM_REQ -c 20 -m POST -S http://localhost:8000/pay > /dev/null
done