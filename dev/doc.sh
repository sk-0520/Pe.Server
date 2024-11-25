#!/bin/bash -ue
cd "$(cd "$(dirname "${0}")"; pwd)"
# cspell:ignore PHPDOC

#shellcheck disable=SC1091
source shell/common.sh
#shellcheck disable=SC2048,SC2086
common::parse_options "phpdoc:cache-path phpdoc:setting-graphs!" $*

PHPDOC_VERSION=v3.5.3
PHPDOC_URL=https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_VERSION}/phpDocumentor.phar
PHPDOC_NAME=phpdoc.phar
PHPDOC_FILE=${PHPDOC_NAME}.${PHPDOC_VERSION}

common::download_phar_if_not_exists "${PHPDOC_FILE}" "${PHPDOC_NAME}" "${PHPDOC_URL}"

PHPDOC_OPTIONS_CACHE_PATH=
if common::exists_option 'phpdoc:cache-path' ; then
	PHPDOC_OPTIONS_CACHE_PATH="--cache-folder=$(common::get_option_value phpdoc:cache-path)"
fi

PHPDOC_OPTIONS_SETTING_GRAPHS=
if common::exists_option 'phpdoc:setting-graphs' ; then
	PHPDOC_OPTIONS_SETTING_GRAPHS="--setting=graphs.enabled=true -vvv"
fi

php "${PHPDOC_FILE}" "${PHPDOC_OPTIONS_CACHE_PATH}" ${PHPDOC_OPTIONS_SETTING_GRAPHS}
