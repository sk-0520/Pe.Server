#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)

PHPSTAN_VERSION=1.8.2
PHPSTAN_URL=https://github.com/phpstan/phpstan/releases/download/${PHPSTAN_VERSION}/phpstan.phar
PHPSTAN_FILE=phpstan.phar.${PHPSTAN_VERSION}
PHPSTAN_BLEEDING_EDGE=bleedingEdge.neon

PHPMD_VERSION=2.12.0
PHPMD_URL=https://github.com/phpmd/phpmd/releases/download/${PHPMD_VERSION}/phpmd.phar
PHPMD_FILE=phpmd.phar.${PHPMD_VERSION}


if [ ! -f ${PHPSTAN_FILE} ] ; then
	rm --force phpstan.phar.*
	curl --output ${PHPSTAN_FILE} --location ${PHPSTAN_URL}
	curl --output ${PHPSTAN_BLEEDING_EDGE} --location https://raw.githubusercontent.com/phpstan/phpstan-src/${PHPSTAN_VERSION}/conf/bleedingEdge.neon
fi
if [ ! -f ${PHPMD_FILE} ] ; then
	rm --force phpmd.phar.*
	curl --output ${PHPMD_FILE} --location ${PHPMD_URL}
fi

if [ ! -v IGNORE_SYNTAX_CHECK ] ; then
	pushd ../public_html
		find . -name '*.php' -not -path './PeServer/Core/Libs/*' -not -path './PeServer/data/*' -exec php --syntax-check {} \;
	popd
	echo 'ignore -> IGNORE_SYNTAX_CHECK'
fi

php "${PHPSTAN_FILE}" analyze --configuration phpstan.neon "$@"
#set +e
#php "${PHPMD_FILE}" ../public_html/PeServer text phpmd.xml "$@"
php "${PHPMD_FILE}" ../public_html/PeServer ansi phpmd.xml "$@"
#set -e
