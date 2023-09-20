#!/bin/bash -ue

pushd "$(cd "$(dirname "${0}")"; pwd)"
	#shellcheck disable=SC1091
	source common.sh
	#shellcheck disable=SC2048,SC2086
	common::parse_options 'test# ignore-namespace! ignore-coverage! phpunit:filter' $*
popd

TEST_MODE=${COMMON_OPTIONS[test]}
case "${TEST_MODE}" in
	ut)
		;;
	*)
		echo "--test [ut]"
		exit 1
		;;
esac

cd "$(cd "$(dirname "${0}")/../test"; pwd)"

BASE_DIR=../public_html

LOCAL_HTTP_TEST="${LOCAL_HTTP_TEST:=localhost:8080}"
LOCAL_HTTP_WAIT="${LOCAL_HTTP_WAIT:=1}"

PHPUNIT_VERSION=10.2.5
PHPUNIT_URL=https://phar.phpunit.de/phpunit-${PHPUNIT_VERSION}.phar
PHPUNIT_NAME=phpunit.phar
PHPUNIT_FILE=${PHPUNIT_NAME}.${PHPUNIT_VERSION}
PHPUNIT_BASE_DIR=../test

common::download_phar_if_not_exists "${PHPUNIT_BASE_DIR}/${PHPUNIT_FILE}" "${PHPUNIT_BASE_DIR}/${PHPUNIT_NAME}" "${PHPUNIT_URL}"

if ! common::exists_option 'ignore-namespace' ; then
	echo "CHECKE NAMESPACE"
	NAMESPACE_ERROR=false

	# Core に App 混入がないか確認
	CORE_DIR=${BASE_DIR}/PeServer/Core
	pushd "${CORE_DIR}"
		#shellcheck disable=SC2044
		for FILE in $(find . -type f -name '*.php') ; do
			if [ "$(grep --count 'PeServer\\App' "${FILE}")" -ne 0 ] ; then
				echo "${FILE}:"
				grep --line-number 'PeServer\\App' "${FILE}"
				NAMESPACE_ERROR=true
			fi
		done
	popd

	# 名前空間がディレクトリとあっているか(オートローダーが死ぬ)
	pushd "${BASE_DIR}"
		#shellcheck disable=SC2044
		for FILE in $(find . \( \( \( -type d -name 'Libs' \) -or \( -type d -name 'deploy' \) -or \( -type d -name 'data' \) -or \( -type f -name index.php \) \) -prune \) -or -type f -name '*.php' -and -print) ; do
			if [ "$(grep --count '^namespace' "${FILE}")" -ne 0 ] ; then
				TARGET_NAMESPACE=${FILE#./} # 先頭の ./ を破棄
				TARGET_NAMESPACE=${TARGET_NAMESPACE%/*} # ファイル名を破棄
				TARGET_NAMESPACE=${TARGET_NAMESPACE//\//\\} # ディレクトリ区切りを名前空間区切りに変換
				SOURCE_NAMESPACE=$(grep '^namespace' "${FILE}")
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

PHPUNIT_OPTION_FILTER=
if common::exists_option 'phpunit:filter' ; then
	PHPUNIT_OPTION_FILTER="--filter ${COMMON_OPTIONS[phpunit:filter]}"
fi

PHPUNIT_OPTION_COVERAGE=
if ! common::exists_option 'ignore-coverage' ; then
	PHPUNIT_OPTION_COVERAGE="--coverage-html ../public_html/public/coverage/php/ut"
fi

COVERAGE_CACHE_OPTION=""
if [[ -v COVERAGE_CACHE ]] ; then
	COVERAGE_CACHE_OPTION="--coverage-cache ${COVERAGE_CACHE}"
fi

cd "${PHPUNIT_BASE_DIR}"
STORAGE="_storage-${TEST_MODE}"
if [[ -d "${STORAGE}" ]] ; then
	rm --recursive --force "${STORAGE}"
fi
mkdir "${STORAGE}"
php -S "${LOCAL_HTTP_TEST}" -t "http-${TEST_MODE}" > "http-${TEST_MODE}.log" 2>&1 &
trap 'kill %1' 0
sleep "${LOCAL_HTTP_WAIT}"
#shellcheck disable=SC2086
php "${PHPUNIT_FILE}" --configuration "../dev/phpunit-${TEST_MODE}.xml" --testdox ${PHPUNIT_OPTION_COVERAGE} ${COVERAGE_CACHE_OPTION} ${PHPUNIT_OPTION_FILTER} .
