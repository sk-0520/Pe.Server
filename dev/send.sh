#!/bin/bash -ue

export SETTING_URL=http://peserver.starfree.jp/deploy/php-deploy-receiver.php
#export SETTING_URL=http://peserver.php.xdomain.jp/deploy/php-deploy-receiver.php
export SETTING_AUTH_HEADER_NAME=DEPLOY
export SETTING_ARCHIVE_FILE_NAME=public_html.zip
export SETTING_SPLIT_SIZE=10MB
export SETTING_LOG_LEVEL=i

$(cd $(dirname $0); pwd)/send-core.sh
