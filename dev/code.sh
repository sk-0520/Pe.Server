#!/bin/bash -ue

cd "$(cd "$(dirname "${0}")"; pwd)"

#shellcheck disable=SC1091
source shell/common.sh
#shellcheck disable=SC2048,SC2086
common::parse_options "ignore-pplint! ignore-phpstan! ignore-phpcs! phpcs-fix! phpcs:report phpcs:ignore-warning! phpcs:cache" $*

PPLINT_VERSION=v1.3.2
PPLINT_URL=https://github.com/php-parallel-lint/PHP-Parallel-Lint/releases/download/${PPLINT_VERSION}/parallel-lint.phar
PPLINT_NAME=parallel-lint.phar
PPLINT_FILE=${PPLINT_NAME}.${PPLINT_VERSION}

PHPSTAN_VERSION=1.10.36
PHPSTAN_URL=https://github.com/phpstan/phpstan/releases/download/${PHPSTAN_VERSION}/phpstan.phar
PHPSTAN_NAME=phpstan.phar
PHPSTAN_FILE=${PHPSTAN_NAME}.${PHPSTAN_VERSION}
PHPSTAN_BLEEDING_EDGE_NAME=bleedingEdge.neon
PHPSTAN_BLEEDING_EDGE_URL=https://raw.githubusercontent.com/phpstan/phpstan-src/${PHPSTAN_VERSION}/conf/bleedingEdge.neon

PHPCODESNIFFER_VERSION=3.7.2
PHPCODESNIFFER_S_URL=https://github.com/squizlabs/PHP_CodeSniffer/releases/download/${PHPCODESNIFFER_VERSION}/phpcs.phar
PHPCODESNIFFER_BF_URL=https://github.com/squizlabs/PHP_CodeSniffer/releases/download/${PHPCODESNIFFER_VERSION}/phpcbf.phar
PHPCODESNIFFER_S_NAME=phpcs.phar
PHPCODESNIFFER_BF_NAME=phpcbf.phar
PHPCODESNIFFER_S_FILE=${PHPCODESNIFFER_S_NAME}.${PHPCODESNIFFER_VERSION}
PHPCODESNIFFER_BF_FILE=${PHPCODESNIFFER_BF_NAME}.${PHPCODESNIFFER_VERSION}

if ! common::exists_option 'ignore-pplint' ; then
	common::download_phar_if_not_exists "${PPLINT_FILE}" "${PPLINT_NAME}" "${PPLINT_URL}"
fi

if ! common::exists_option 'ignore-phpstan' ; then
	common::download_phar_if_not_exists "${PHPSTAN_FILE}" "${PHPSTAN_NAME}" "${PHPSTAN_URL}"
	if [ "${COMMON_DOWNLOAD_PHAR_RESULT}" = "DOWNLOAD" ] ; then
		curl --output "${PHPSTAN_BLEEDING_EDGE_NAME}" --location "${PHPSTAN_BLEEDING_EDGE_URL}"
	fi
fi

if ! common::exists_option 'ignore-phpcs' ; then
	common::download_phar_if_not_exists "${PHPCODESNIFFER_S_FILE}" "${PHPCODESNIFFER_S_NAME}" "${PHPCODESNIFFER_S_URL}"
	if [ "${COMMON_DOWNLOAD_PHAR_RESULT}" = "DOWNLOAD" ] ; then
		common::download_phar_if_not_exists "${PHPCODESNIFFER_BF_FILE}" "${PHPCODESNIFFER_BF_NAME}" "${PHPCODESNIFFER_BF_URL}"
	fi
fi

if ! common::exists_option 'ignore-pplint' ; then
	php "${PPLINT_FILE}" ../public_html/PeServer --colors --show-deprecated --exclude ../public_html/PeServer/Core/Libs  --exclude ../public_html/PeServer/data
fi

#php "${PHPCSFIXER_FILE}" fix --dry-run --diff ../public_html/PeServer  "$@"

if ! common::exists_option 'ignore-phpstan' ; then
	php "${PHPSTAN_FILE}" analyze --configuration phpstan.neon
fi

if ! common::exists_option 'ignore-phpcs' ; then
	PHPCS_OPTION_REPORT="--report=full"
	if common::exists_option 'phpcs:report' ; then
		PHPCS_OPTION_REPORT="$(common::get_option_value phpcs:report)"
	fi

	PHPCS_OPTIONS_WARNIG=
	if common::exists_option 'phpcs:ignore-warning' ; then
		PHPCS_OPTIONS_WARNIG="--warning-severity=0"
	fi

	PHPCS_OPTIONS_CACHE=
	if common::exists_option 'phpcs:cache' ; then
		PHPCS_OPTIONS_CACHE="--cache $(common::get_option_value phpcs:cache)"
	fi


	PHPCS_OPTIONS_DEFAULT="../public_html/PeServer --standard=phpcs_ruleset.xml"

	if common::exists_option 'phpcs-fix' ; then
		logger::info "!!修正処理実施!!"

		#shellcheck disable=SC2086
		php "${PHPCODESNIFFER_BF_FILE}" ${PHPCS_OPTIONS_DEFAULT}
	fi

	#shellcheck disable=SC2086
	php "${PHPCODESNIFFER_S_FILE}" ${PHPCS_OPTIONS_DEFAULT} ${PHPCS_OPTION_REPORT} ${PHPCS_OPTIONS_WARNIG} ${PHPCS_OPTIONS_CACHE}
fi

#set +e
#php "${PHPMD_FILE}" ../public_html/PeServer text phpmd.xml "$@"
#[PHPMD] php "${PHPMD_FILE}" ../public_html/PeServer ansi phpmd.xml "$@"
#set -e
