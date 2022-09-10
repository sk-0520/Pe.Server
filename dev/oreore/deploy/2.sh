#!/bin/bash -ue

. ./@env.sh

FILE=public_html.zip

curl -X POST \
	-H "X-API-KEY: ${DEPLOY_API_KEY}" \
	-H "X-SECRET-KEY: ${DEPLOY_API_SEC}" \
	${URL_BASE}/upload --form "file=@$FILE" --form "sequence=0"



