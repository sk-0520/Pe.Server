#!/bin/bash -ue

cd $(cd $(dirname $0)/../test; pwd)

NAMESPACE_ERROR=false
CORE_DIR=../public_html/PeServer/Core
pushd $CORE_DIR
	for FILE in $(find . -name '*.php') ; do
		if [ $(grep --count 'PeServer\\App' "${FILE}") -ne 0 ] ; then
			echo "${FILE}:"
			grep --line-number 'PeServer\\App' "${FILE}"
			NAMESPACE_ERROR=true
		fi
	done
popd

if "${NAMESPACE_ERROR}" ; then
	echo "namespace error!"
	exit 1
fi

php phpunit.phar --bootstrap ./bootstrap.php --testdox .
