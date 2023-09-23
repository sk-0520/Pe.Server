#!/bin/bash -ue

cd "$(cd "$(dirname "${0}")"; pwd)"

#shellcheck disable=SC1091
source shell/common.sh
#common::parse_options "none" $*

PHPDOC_VERSION=v3.4.1
PHPDOC_URL=https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_VERSION}/phpDocumentor.phar
PHPDOC_NAME=phpdoc.phar
PHPDOC_FILE=${PHPDOC_NAME}.${PHPDOC_VERSION}

common::download_phar_if_not_exists "${PHPDOC_FILE}" "${PHPDOC_NAME}" "${PHPDOC_URL}"

php "${PHPDOC_FILE}"
