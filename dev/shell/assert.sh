#!/bin/bash -ue

# テストスクリプト処理用スクリプト
#
# このスクリプトは依存関係を持たない
#
# テストスクリプトから assert::test を実行することでテストが頑張りだす
# んでテストスクリプトを規定のディレクトリに配置していれば assert::tests を呼び出すことですべて実行する
#
# 本スクリプトはテストスクリプトと全体実行スクリプトから読み込まれる前提
# テスト関数は test_ で始まる関数名を実行する(順不同)
#
# テストスクリプトに以下の関数が含まれている場合は独別処理を行う
#
# startup   テストスクリプト全体実行時に実行(引数なし)
# shutdown  テストスクリプト全体実行後に実行(引数なし)
# setup     テストスクリプトの各テスト実行時に実行(1: 関数名)
# teardown  テストスクリプトの各テスト実行後に実行(1: 関数名)

# 本スクリプトを読み込んだテストスクリプトファイル
_ASSERT_FILE_NAME=$0
# 本スクリプトを読み込んだテストスクリプトの成功状態
_ASSERT_FILE_SUCCESS=true

# 現在実行中のテスト関数
_ASSERT_CURRENT_FUNCTION=
# 直近のテスト関数成功状態
_ASSERT_CURRENT_SUCCESS=false

# エラー設定
# 非公開
function assert::_set_error
{
	_ASSERT_CURRENT_SUCCESS=false
	_ASSERT_FILE_SUCCESS=false
}

function assert::_output_error
{
	echo "$*" >&2
}

function assert::_write_error
{
	echo -en "\e[31m"
	echo -n "$*"
	echo -en "\e[m"
}

function assert::_write_warning
{
	echo -en "\e[33m"
	echo -n "$*"
	echo -en "\e[m"
}

function assert::_write_success
{
	echo -en "\e[32m"
	echo -n "$*"
	echo -en "\e[m"
}

function assert::_write_mute
{
	echo -en "\e[30m"
	echo -n "$*"
	echo -en "\e[m"
}


function assert::_write_break
{
	echo ''
}

# 指定された関数が有効であれば実行
# 非公開
function assert::_call_enable_function
{
	local FUNC_NAME="${1}"
	if [ -z "${FUNC_NAME}" ] ; then
		return 0
	fi

	local ARG="${2}"
	if [[ -v "${ARG}" ]] ; then
		${FUNC_NAME} "${ARG}"
	else
		${FUNC_NAME}
	fi
}

function assert::success
{
	:
}

function assert::failuer {
	assert::_set_error
	assert::_output_error "${BASH_LINENO[0]}: ${FUNCNAME[0]}"
}


# 戻り値が成功(0)
#
# 以下の使用を想定している
# assert::is_success $?
function assert::is_success
{
	if [[ "${1}" != '0' ]] ; then
		assert::_set_error
		assert::_output_error "${BASH_LINENO[0]}: ${FUNCNAME[0]} -> ${1}"
	fi
}

# 戻り値が成功(!0)
#
# 以下の使用を想定している
# assert::is_failuer $?
function assert::is_failuer
{
	if [[ "${1}" == '0' ]] ; then
		assert::_set_error
		assert::_output_error "${BASH_LINENO[0]}: ${FUNCNAME[0]} -> ${1}"
	fi
}

# 二つの値が同じ
#
# 以下の使用を想定している
# assert::equals "expected" "${VAR}"
function assert::equals
{
	if [[ "$1" != "$2" ]]  ; then
		assert::_set_error
		assert::_output_error "${BASH_LINENO[0]}: ${FUNCNAME[0]} -> ${1} != ${2}"
	fi
}

# 開始時に実施
# 内部使用
function assert::_startup
{
	echo -n '[START] '
	assert::_write_mute "${_ASSERT_FILE_NAME}"
	echo ""
}

# 終了時に実施
# 内部使用
function assert::_shutdown
{
	if [ ${_ASSERT_FILE_SUCCESS} = true ] ; then
		echo -n "[$(assert::_write_success SUCCESS)] "
		assert::_write_mute "${_ASSERT_FILE_NAME}"
		assert::_write_break
		exit 0
	fi

	echo -n "[$(assert::_write_error FAILUER)] "
	assert::_write_warning "${_ASSERT_FILE_NAME}"
	assert::_write_break
	exit 1
}


# テストスクリプトの最後に呼び出すテスト実行処理
#
# テストスクリプト内で有効なテスト関数をすべて実行する
function assert::test
{
	local FUNCTIONS
	FUNCTIONS=$(compgen -A function | grep --invert-match '^assert::')

	# echo "${FUNCTIONS}" | grep 'startup'
	# echo "${FUNCTIONS}" | grep 'shutdown'
	FUNCTION_STARTUP=$(echo "${FUNCTIONS}" | grep '^startup$') || printf ''
	FUNCTION_SHUTDOWN=$(echo "${FUNCTIONS}" | grep '^shutdown$')|| printf ''
	FUNCTION_SETUP=$(echo "${FUNCTIONS}" | grep '^setup$')|| printf ''
	FUNCTION_TEARDOWN=$(echo "${FUNCTIONS}" | grep '^teardown$')|| printf ''

	local TEMP_MESSAGE
	TEMP_MESSAGE="${BASH_SOURCE[0]}.tmp"

	local TEST_FUNCS
	TEST_FUNCS=$(echo "${FUNCTIONS}" | grep '^test_')

	assert::_startup
	trap 'assert::_shutdown' 0

	assert::_call_enable_function "${FUNCTION_STARTUP}"

	for FUNC in ${TEST_FUNCS} ; do
		_ASSERT_CURRENT_SUCCESS=true
		_ASSERT_CURRENT_FUNCTION="${FUNC}"

		assert::_call_enable_function "${FUNCTION_SETUP}" "${FUNC}"

		# コマンド置き換えするとサブシェル内でのグローバル変数が連携されないのでリダイレクトでエラーを取得
		set +e
		#MESSAGE=$(${FUNC})
		${FUNC} > /dev/null 2> "${TEMP_MESSAGE}"

		if [ ${_ASSERT_CURRENT_SUCCESS} = true ] ; then
			assert::_write_success "✅ ${FUNC}"
		else
			assert::_write_error "❌ ${FUNC}"
		fi
		assert::_write_break
		set -e

		if [ ${_ASSERT_CURRENT_SUCCESS} = false ] ; then
			while IFS= read -r LINE ; do
				# 制御コードは外す
				local TEXT_LINE
				TEXT_LINE="$(echo "${LINE}" | sed -r "s/\x1B\[([0-9]{1,2}(;[0-9]{1,2})?)?[mGK]//g")"

				if [[ -n "${TEXT_LINE}" ]] ; then
					assert::_write_warning "  ${TEXT_LINE}"
					assert::_write_break
				fi
			done < "${TEMP_MESSAGE}"
		fi
		assert::_call_enable_function "${FUNCTION_TEARDOWN}" "${FUNC}"
	done

	assert::_call_enable_function "${FUNCTION_SHUTDOWN}"
}

# テストスクリプトの一括実行
#
# テストスクリプトファイルを列挙し、それぞれのテストを実行する
# テスト実行処理の親となるスクリプトから呼び出される想定
#
# TODO: 環境変数とかで設定を渡せるようにしておきたい
function assert::tests
{
	#TODO ディレクトリとか制御する必要あり
	declare -a ERROR_FILES=()

	for FILE in test/*.test.sh ; do
		set +e
		"./${FILE}"
		local RESULT=$?
		set -e

		if [[ ${RESULT} != 0 ]] ; then
			ERROR_FILES+=("${FILE}")
		fi
	done

	if [[ ${#ERROR_FILES[@]} != 0 ]] ; then
		assert::_write_error "ERROR:"
		assert::_write_break

		for ERROR_FILE in "${ERROR_FILES[@]}"
		do
			assert::_write_warning "  ${ERROR_FILE}"
			assert::_write_break
		done
		exit 1
	fi

	exit 0
}
