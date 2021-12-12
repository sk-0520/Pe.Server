#!/bin/bash -ue

PROGRAM_DIR=$(cd $(dirname $0)/../public_html/PeServer; pwd)
EXPORT_DIR=$(dirname $0)/../public_html/-PeServer

echo $PROGRAM_DIR

if [ -d "${EXPORT_DIR}" ] ; then
	rm -rf "${EXPORT_DIR}"
fi

cp -rfv "${PROGRAM_DIR}" "${EXPORT_DIR}"

for FILE in `find ${EXPORT_DIR} -type f -name '*.php'` ; do
	sed -i -e '/^namespace/d' $FILE
	sed -i -e '/^use/d' $FILE
done
