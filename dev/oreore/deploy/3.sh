#!/bin/bash -ue

# shellcheck disable=SC1091
. ./@env.sh

curl -X POST \
	-H "X-API-KEY: ${DEPLOY_API_KEY}" \
	-H "X-SECRET-KEY: ${DEPLOY_API_SEC}" \
	"${URL_BASE}/prepare"

