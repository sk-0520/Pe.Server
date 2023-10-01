#!/bin/bash -ue

pushd "$(cd "$(dirname "${0}")"; pwd)"
	#shellcheck disable=SC1091
	source shell/common.sh
	#shellcheck disable=SC2048,SC2086
	common::parse_options 'mode|ut|it|st|uit# no-exit! ignore-namespace! ignore-coverage! phpunit:filter phpunit:exclude-group' $*
popd

TEST_MODE="$(common::get_option_value mode)"

# サーバー側引き渡し用(bootstrapでも流用)
export APP_TEST_MODE="${TEST_MODE}"

cd "$(cd "$(dirname "${0}")/../test"; pwd)"

BASE_DIR=../public_html

LOCAL_HTTP_TEST="${LOCAL_HTTP_TEST:=localhost:8080}"
LOCAL_HTTP_WAIT="${LOCAL_HTTP_WAIT:=1}"

PHPUNIT_VERSION=10.3.5
PHPUNIT_URL=https://phar.phpunit.de/phpunit-${PHPUNIT_VERSION}.phar
PHPUNIT_NAME=phpunit.phar
PHPUNIT_FILE=${PHPUNIT_NAME}.${PHPUNIT_VERSION}
PHPUNIT_BASE_DIR=../test

common::download_phar_if_not_exists "${PHPUNIT_BASE_DIR}/${PHPUNIT_FILE}" "${PHPUNIT_BASE_DIR}/${PHPUNIT_NAME}" "${PHPUNIT_URL}"

if ! common::exists_option 'ignore-namespace' ; then
	logger::info "[CHECKE NAMESPACE]"

	NAMESPACE_ERROR=false

	# Core に App 混入がないか確認
	CORE_DIR=${BASE_DIR}/PeServer/Core
	pushd "${CORE_DIR}"
		#shellcheck disable=SC2044
		for FILE in $(find . -type f -name '*.php') ; do
			if [ "$(grep --count 'PeServer\\App' "${FILE}")" -ne 0 ] ; then
				logger::info "${FILE}:"
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
					logger::error "${FILE}: ${SOURCE_NAMESPACE} != ${TARGET_NAMESPACE}"
					NAMESPACE_ERROR=true
				fi
			fi
		done
	popd

	if "${NAMESPACE_ERROR}" ; then
		logger::error "namespace error!"
		exit 1
	fi
fi

PHPUNIT_OPTION_FILTER=
if common::exists_option 'phpunit:filter' ; then
	PHPUNIT_OPTION_FILTER="--filter $(common::get_option_value phpunit:filter)"
fi
PHPUNIT_OPTION_EXCLUDE_GROUP=
if common::exists_option 'phpunit:exclude-group' ; then
	PHPUNIT_OPTION_EXCLUDE_GROUP="--exclude-group $(common::get_option_value phpunit:exclude-group)"
fi


PHPUNIT_OPTION_COVERAGE=
if ! common::exists_option 'ignore-coverage' ; then
	PHPUNIT_OPTION_COVERAGE="--coverage-html ../public_html/public/coverage/php/${TEST_MODE}"
fi

COVERAGE_CACHE_OPTION=""
if [[ -v COVERAGE_CACHE ]] ; then
	COVERAGE_CACHE_OPTION="--coverage-cache ${COVERAGE_CACHE}"
fi

PUBLIC_DIR=
TEST_SUITE="--testsuite ${TEST_MODE}"
case "${TEST_MODE}" in
	ut | it)
		PUBLIC_DIR="${PHPUNIT_BASE_DIR}/http-${TEST_MODE}"
		;;
	uit )
		PUBLIC_DIR="${PHPUNIT_BASE_DIR}/http-ut"
		TEST_SUITE="--testsuite ut,it"
		;;
	st)
		PUBLIC_DIR="${BASE_DIR}"
		;;
	*)
		exit 255
		;;
esac

cd ${PHPUNIT_BASE_DIR}/
STORAGE="storage-${TEST_MODE}"
if [[ -d "${STORAGE}" ]] ; then
	rm --recursive --force "${STORAGE}"
fi
mkdir "${STORAGE}"

php -S "${LOCAL_HTTP_TEST}" -t "${PUBLIC_DIR}" > "http-${TEST_MODE}.log" 2>&1 &
trap 'kill %1' 0
sleep "${LOCAL_HTTP_WAIT}"
#shellcheck disable=SC2086
php "${PHPUNIT_FILE}"  --configuration "../dev/phpunit.xml" ${TEST_SUITE} ${PHPUNIT_OPTION_COVERAGE} ${COVERAGE_CACHE_OPTION} ${PHPUNIT_OPTION_FILTER} ${PHPUNIT_OPTION_EXCLUDE_GROUP}

if common::exists_option 'no-exit' ; then
	read -r
fi
