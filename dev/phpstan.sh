#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)

PHPSTAN_URL=https://github.com/phpstan/phpstan/releases/download/1.4.5/phpstan.phar
PHPSTAN_FILE=phpstan.phar

if [ ! -f ${PHPSTAN_FILE} ] ; then
	curl --output ${PHPSTAN_FILE} --location ${PHPSTAN_URL}
fi

if [ ! -v IGNORE_SYNTAX_CHECK ] ; then
	pushd ../public_html
		find . -name '*.php' -not -path './PeServer/Core/Libs/*' -not -path './PeServer/data/*' -exec php --syntax-check {} \;
	popd
	echo 'ignore -> IGNORE_SYNTAX_CHECK'
fi

php "${PHPSTAN_FILE}" analyze --configuration phpstan.neon
