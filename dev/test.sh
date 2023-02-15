#!/bin/bash -ue

cd $(cd $(dirname $0)/../test; pwd)

BASE_DIR=../public_html

PHPUNIT_VERSION=9.6.3
PHPUNIT_URL=https://phar.phpunit.de/phpunit-${PHPUNIT_VERSION}.phar
PHPUNIT_FILE=phpunit.phar.${PHPUNIT_VERSION}
PHPUNIT_BASE_DIR=../test

if [ ! -f ${PHPUNIT_BASE_DIR}/${PHPUNIT_FILE} ] ; then
	rm --force ${PHPUNIT_BASE_DIR}/phpunit.phar.*
	curl --output ${PHPUNIT_BASE_DIR}/${PHPUNIT_FILE} --location ${PHPUNIT_URL}
fi

if [ ! -v IGNORE_NAMESPACE_CHECK ] ; then
	NAMESPACE_ERROR=false

	# Core に App 混入がないか確認
	CORE_DIR=${BASE_DIR}/PeServer/Core
	pushd "$CORE_DIR"
		for FILE in $(find . -type f -name '*.php') ; do
			if [ $(grep --count 'PeServer\\App' "${FILE}") -ne 0 ] ; then
				echo "${FILE}:"
				grep --line-number 'PeServer\\App' "${FILE}"
				NAMESPACE_ERROR=true
			fi
		done
	popd

	# 名前空間がディレクトリとあっているか(オートローダーが死ぬ)
	pushd "${BASE_DIR}"
		for FILE in $(find . \( \( \( -type d -name 'Libs' \) -or \( -type d -name 'deploy' \) -or \( -type d -name 'data' \) -or \( -type f -name index.php \) \) -prune \) -or -type f -name '*.php' -and -print) ; do
			if [ $(grep --count '^namespace' ${FILE}) -ne 0 ] ; then
				TARGET_NAMESPACE=${FILE#./} # 先頭の ./ を破棄
				TARGET_NAMESPACE=${TARGET_NAMESPACE%/*} # ファイル名を破棄
				TARGET_NAMESPACE=${TARGET_NAMESPACE//\//\\} # ディレクトリ区切りを名前空間区切りに変換
				SOURCE_NAMESPACE=$(grep '^namespace' $FILE)
				SOURCE_NAMESPACE=${SOURCE_NAMESPACE#namespace}
				SOURCE_NAMESPACE=${SOURCE_NAMESPACE%;*}
				SOURCE_NAMESPACE=${SOURCE_NAMESPACE// /}
				SOURCE_NAMESPACE=${SOURCE_NAMESPACE//	/}
				if [ "${TARGET_NAMESPACE}" != "${SOURCE_NAMESPACE}" ] ; then
					echo "${FILE}: ${SOURCE_NAMESPACE} != ${TARGET_NAMESPACE}"
					NAMESPACE_ERROR=true
				fi
			fi
		done
	popd

	echo 'ignore -> IGNORE_NAMESPACE_CHECK'

	if "${NAMESPACE_ERROR}" ; then
		echo "namespace error!"
		exit 1
	fi
fi

COVERAGE_CACHE_OPTION=""
if [[ -v COVERAGE_CACHE ]] ; then
	COVERAGE_CACHE_OPTION="--coverage-cache ${COVERAGE_CACHE}"
fi
php ${PHPUNIT_FILE} --configuration ../dev/phpunit.xml --coverage-html ../public_html/public/coverage/php --testdox $COVERAGE_CACHE_OPTION "$@" .
