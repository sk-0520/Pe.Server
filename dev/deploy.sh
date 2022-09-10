#!/bin/bash -ue

export SETTING_URL=http://localhost/api/administrator/deploy
export SETTING_ARCHIVE_FILE_NAME=public_html.zip
export SETTING_SPLIT_SIZE=10MB
export SETTING_LOG_LEVEL=t

export API_KEY=$1
export API_SECRET=$2

$(cd $(dirname $0); pwd)/deploy-core.sh
