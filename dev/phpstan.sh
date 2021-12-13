#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)

PHPSTAN_URL=https://github.com/phpstan/phpstan/releases/download/1.2.0/phpstan.phar
PHPSTAN_FILE=phpstan.phar

if [ ! -f ${PHPSTAN_FILE} ] ; then
	curl --output ${PHPSTAN_FILE} --location ${PHPSTAN_URL}
fi

pushd ../public_html
	find . -name '*.php' -not -path './PeServer/Libs/*' -not -path './PeServer/data/*' -exec php --syntax-check {} \;
popd

php "${PHPSTAN_FILE}" analyze --configuration phpstan.neon