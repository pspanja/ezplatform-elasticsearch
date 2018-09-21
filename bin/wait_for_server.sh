#!/usr/bin/env bash

SERVER=${1:-"http://localhost:9200"}

is_server_up(){
    http_code=`echo $(curl -s -o /dev/null -w "%{http_code}" ${SERVER})`
    return `test ${http_code} = "200"`
}

while ! is_server_up; do
    sleep 3
done
