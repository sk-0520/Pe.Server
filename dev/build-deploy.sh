#!/bin/bash -ue

cd $(cd $(dirname $0); pwd)

INPUT_FILE=../public_html/deploy/php-deploy-receiver.php
TEMP_FILE=../public_html/deploy/-php-deploy-receiver.tmp
OUTPUT_FILE=../public_html/deploy/-php-deploy-receiver.php
SOURCE_DIR=../public_html
rm --force ${TEMP_FILE}
rm --force ${OUTPUT_FILE}

cp "${INPUT_FILE}" "${TEMP_FILE}"
sed -i -n "/^\/\/AUTO-GEN-CODE/q;p" "${TEMP_FILE}"

echo "" >> "${TEMP_FILE}"
echo "//AUTO-GEN-CODE" >> "${TEMP_FILE}"

FILE_SETTINGS=$(grep -E --only-matching '^//AUTO-GEN-SETTING:FILE:(.*)$' ${TEMP_FILE})
for FILE_SETTING in $FILE_SETTINGS ; do
	FILE_PATH=${FILE_SETTING#*FILE:}
	cat "${SOURCE_DIR}/${FILE_PATH}" \
	| sed -e '/^<?php/d' \
	| sed -e '/^$/d' \
	| sed -e '/^declare/d' \
	| sed -e '/^namespace/d' \
	| sed -e '/^use/d' \
	>> "${TEMP_FILE}"
done

cp "${TEMP_FILE}" "${OUTPUT_FILE}"
