#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)

PHPSTAN_VERSION=1.7.7
PHPSTAN_URL=https://github.com/phpstan/phpstan/releases/download/${PHPSTAN_VERSION}/phpstan.phar
PHPSTAN_FILE=${PHPSTAN_VERSION}.phpstan.phar

if [ ! -f ${PHPSTAN_FILE} ] ; then
	rm --force *.phpstan.phar
	curl --output ${PHPSTAN_FILE} --location ${PHPSTAN_URL}
fi

if [ ! -v IGNORE_SYNTAX_CHECK ] ; then
	pushd ../public_html
		find . -name '*.php' -not -path './PeServer/Core/Libs/*' -not -path './PeServer/data/*' -exec php --syntax-check {} \;
	popd
	echo 'ignore -> IGNORE_SYNTAX_CHECK'
fi

php "${PHPSTAN_FILE}" analyze --configuration phpstan.neon "$@"
