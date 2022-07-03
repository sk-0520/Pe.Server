#!/bin/bash -ue

echo -n 'EXPORT: '
read EXPORT_NAME

PROGRAM_DIR=$(cd $(dirname $0)/../public_html/PeServer; pwd)
EXPORT_DIR=$(dirname $0)/../public_html/-${EXPORT_NAME}

if [ -d "${EXPORT_DIR}" ] ; then
	rm -rf "${EXPORT_DIR}"
fi

cp -rfv "${PROGRAM_DIR}" "${EXPORT_DIR}"

for FILE in `find ${EXPORT_DIR} -type f -name '*.php'` ; do
	sed -i "s/PeServer/${EXPORT_NAME}/g" $FILE
done
