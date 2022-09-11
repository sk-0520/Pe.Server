#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)


PPLINT_VERSION=v1.3.2
PPLINT_URL=https://github.com/php-parallel-lint/PHP-Parallel-Lint/releases/download/${PPLINT_VERSION}/parallel-lint.phar
PPLINT_NAME=parallel-lint.phar
PPLINT_FILE=${PPLINT_NAME}.${PPLINT_VERSION}

# PHPCSFIXER_VERSION=v3.10.0
# PHPCSFIXER_URL=https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/${PHPCSFIXER_VERSION}/php-cs-fixer.phar
# PHPCSFIXER_NAME=php-cs-fixer.phar
# PHPCSFIXER_FILE=${PHPCSFIXER_NAME}.${PHPCSFIXER_VERSION}

PHPSTAN_VERSION=1.8.4
PHPSTAN_URL=https://github.com/phpstan/phpstan/releases/download/${PHPSTAN_VERSION}/phpstan.phar
PHPSTAN_NAME=phpstan.phar
PHPSTAN_FILE=${PHPSTAN_NAME}.${PHPSTAN_VERSION}
PHPSTAN_BLEEDING_EDGE=bleedingEdge.neon

PHPMD_VERSION=2.12.0
PHPMD_URL=https://github.com/phpmd/phpmd/releases/download/${PHPMD_VERSION}/phpmd.phar
PHPMD_NAME=phpmd.phar
PHPMD_FILE=${PHPMD_NAME}.${PHPMD_VERSION}

if [ ! -f ${PPLINT_FILE} ] ; then
	rm --force ${PPLINT_NAME}.*
	curl --output ${PPLINT_FILE} --location ${PPLINT_URL}
fi
# if [ ! -f ${PHPCSFIXER_FILE} ] ; then
# 	rm --force ${PHPCSFIXER_NAME}.*
# 	curl --output ${PHPCSFIXER_FILE} --location ${PHPCSFIXER_URL}
# fi
if [ ! -f ${PHPSTAN_FILE} ] ; then
	rm --force ${PHPSTAN_NAME}.*
	curl --output ${PHPSTAN_FILE} --location ${PHPSTAN_URL}
	curl --output ${PHPSTAN_BLEEDING_EDGE} --location https://raw.githubusercontent.com/phpstan/phpstan-src/${PHPSTAN_VERSION}/conf/bleedingEdge.neon
fi
if [ ! -f ${PHPMD_FILE} ] ; then
	rm --force ${PHPMD_NAME}.*
	curl --output ${PHPMD_FILE} --location ${PHPMD_URL}
fi

# if [ ! -v IGNORE_SYNTAX_CHECK ] ; then
# 	pushd ../public_html
# 		find . -name '*.php' -not -path './PeServer/Core/Libs/*' -not -path './PeServer/data/*' -exec php --syntax-check {} \;
# 	popd
# 	echo 'ignore -> IGNORE_SYNTAX_CHECK'
# fi
php "${PPLINT_FILE}" ../public_html/PeServer --colors --show-deprecated --exclude ../public_html/PeServer/Core/Libs  --exclude ../public_html/PeServer/data "$@"

#php "${PHPCSFIXER_FILE}" fix --dry-run --diff ../public_html/PeServer  "$@"

php "${PHPSTAN_FILE}" analyze --configuration phpstan.neon "$@"
#set +e
#php "${PHPMD_FILE}" ../public_html/PeServer text phpmd.xml "$@"
php "${PHPMD_FILE}" ../public_html/PeServer ansi phpmd.xml "$@"
#set -e
