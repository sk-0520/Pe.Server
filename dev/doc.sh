#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)

PHPDOC_VERSION=v3.4.1
PHPDOC_URL=https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_VERSION}/phpDocumentor.phar
PHPDOC_NAME=phpdoc.phar
PHPDOC_FILE=${PHPDOC_NAME}.${PHPDOC_VERSION}

if [ ! -f ${PHPDOC_FILE} ] ; then
	rm --force ${PHPDOC_NAME}.*
	curl --output ${PHPDOC_FILE} --location ${PHPDOC_URL}
fi

php "${PHPDOC_FILE}" "$@"
