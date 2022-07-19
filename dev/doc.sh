#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)

PHPDOC_VERSION=v3.3.1
PHPDOC_URL=https://github.com/phpDocumentor/phpDocumentor/releases/download/${PHPDOC_VERSION}/phpDocumentor.phar
PHPDOC_FILE=phpdoc.phar.${PHPDOC_VERSION}

if [ ! -f ${PHPDOC_FILE} ] ; then
	rm --force phpdoc.phar.*
	curl --output ${PHPDOC_FILE} --location ${PHPDOC_URL}
fi

php "${PHPDOC_FILE}" "$@"
