#!/bin/bash -ue

export SETTING_API_URL=https://peserver.site/api/administrator/deploy
export SETTING_ARCHIVE_FILE_NAME=public_html.zip
export SETTING_SPLIT_SIZE=4MB
export SETTING_LOG_LEVEL=t

export SETTING_API_KEY=$1
export SETTING_API_SECRET=$2

$(cd $(dirname $0); pwd)/deploy-core.sh
