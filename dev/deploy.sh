#!/bin/bash -ue
cd "$(cd "$(dirname "${0}")"; pwd)"

export SETTING_API_URL=https://peserver.site/api/administrator/deploy
export SETTING_SPLIT_SIZE=4MB
export SETTING_LOG_LEVEL=t

export SETTING_ARCHIVE_FILE_NAME=$1
export SETTING_API_KEY=$2
export SETTING_API_SECRET=$3

./deploy-core.sh
