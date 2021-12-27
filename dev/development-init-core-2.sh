#!/bin/bash -ue

DEV_URL="${DEV_URL:=http://localhost}"

API_URL=${DEV_URL}/api/development/administrator

curl -v -X POST ${API_URL}
