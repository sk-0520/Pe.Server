#!/bin/bash -ue
cd "$(cd "$(dirname "${0}")"; pwd)"
# cspell:ignore PPLINT PHPCODESNIFFER phpcbf ruleset

#shellcheck disable=SC1091
source shell/common.sh
common::parse_options "ignore-pplint? ignore-phpstan? ignore-phpcs? phpcs-fix? phpcs:report phpcs:ignore-warning? phpcs:cache" "$@"

PPLINT_VERSION=v1.4.0
PPLINT_URL=https://github.com/php-parallel-lint/PHP-Parallel-Lint/releases/download/${PPLINT_VERSION}/parallel-lint.phar
PPLINT_NAME=parallel-lint.phar
PPLINT_FILE=${PPLINT_NAME}.${PPLINT_VERSION}

PHPSTAN_VERSION=2.1.16
PHPSTAN_URL=https://github.com/phpstan/phpstan/releases/download/${PHPSTAN_VERSION}/phpstan.phar
PHPSTAN_NAME=phpstan.phar
PHPSTAN_FILE=${PHPSTAN_NAME}.${PHPSTAN_VERSION}
# PHPSTAN_BLEEDING_EDGE_NAME=bleedingEdge.neon
# PHPSTAN_BLEEDING_EDGE_URL=https://raw.githubusercontent.com/phpstan/phpstan-src/${PHPSTAN_VERSION}/conf/bleedingEdge.neon

PHPCODESNIFFER_VERSION=3.12.2
PHPCODESNIFFER_S_URL=https://github.com/PHPCSStandards/PHP_CodeSniffer/releases/download/${PHPCODESNIFFER_VERSION}/phpcs.phar
PHPCODESNIFFER_BF_URL=https://github.com/PHPCSStandards/PHP_CodeSniffer/releases/download/${PHPCODESNIFFER_VERSION}/phpcbf.phar
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
		# curl --output "${PHPSTAN_BLEEDING_EDGE_NAME}" --location "${PHPSTAN_BLEEDING_EDGE_URL}"
		cp "${PHPSTAN_FILE}" "${PHPSTAN_NAME}"
	fi
fi

if ! common::exists_option 'ignore-phpcs' ; then
	common::download_phar_if_not_exists "${PHPCODESNIFFER_S_FILE}" "${PHPCODESNIFFER_S_NAME}" "${PHPCODESNIFFER_S_URL}"
	if [ "${COMMON_DOWNLOAD_PHAR_RESULT}" = "DOWNLOAD" ] ; then
		common::download_phar_if_not_exists "${PHPCODESNIFFER_BF_FILE}" "${PHPCODESNIFFER_BF_NAME}" "${PHPCODESNIFFER_BF_URL}"
	fi
fi

if ! common::exists_option 'ignore-pplint' ; then
	php "${PPLINT_FILE}" ../PeServer --colors --show-deprecated --exclude ../PeServer/Core/Libs  --exclude ../PeServer/data
fi

if ! common::exists_option 'ignore-phpstan' ; then
	php "${PHPSTAN_FILE}" -v analyze --configuration phpstan.neon --memory-limit=1G
fi

if ! common::exists_option 'ignore-phpcs' ; then
	PHPCS_OPTION_REPORT="--report=full"
	if common::exists_option 'phpcs:report' ; then
		PHPCS_OPTION_REPORT="$(common::get_option_value phpcs:report)"
	fi

	PHPCS_OPTIONS_WARNING=
	if common::exists_option 'phpcs:ignore-warning' ; then
		PHPCS_OPTIONS_WARNING="--warning-severity=0"
	fi

	PHPCS_OPTIONS_CACHE=
	if common::exists_option 'phpcs:cache' ; then
		PHPCS_OPTIONS_CACHE="--cache $(common::get_option_value phpcs:cache)"
	fi


	PHPCS_OPTIONS_DEFAULT="../PeServer ../test/PeServer* --standard=phpcs_ruleset.xml"

	if common::exists_option 'phpcs-fix' ; then
		logger::info "!!修正処理実施!!"

		#shellcheck disable=SC2086
		php "${PHPCODESNIFFER_BF_FILE}" ${PHPCS_OPTIONS_DEFAULT}
	fi

	#shellcheck disable=SC2086
	php "${PHPCODESNIFFER_S_FILE}" ${PHPCS_OPTIONS_DEFAULT} ${PHPCS_OPTION_REPORT} ${PHPCS_OPTIONS_WARNING} ${PHPCS_OPTIONS_CACHE}
fi
