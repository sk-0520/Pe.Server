#!/bin/bash -ue
# 共通処理まとめない無理だこれ
#
# * 使用側から `source common.sh` で取り込まれることを想定している
# * 二重に読み込まれることは想定していない
# * 以下モジュールが依存モジュールとして読み込まれる
#   * logger.sh

#shellcheck disable=SC1091
source "$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" &> /dev/null && pwd)/logger.sh"

# コマンドライン引数の辞書変数
# 決まりを守って正しく使う
declare -A _COMMON_OPTIONS=()

# コマンドライン引数($_COMMON_OPTIONS)を使用可能にする。
#
# 使用可能にするが出来る限りヘルパ関数を使用すること。
#
# オプションは --name value もしくは --switch 形式のみを受け付ける
#
# 以下の使用を想定している
# #shellcheck disable=SC2048,SC2086
# common::parse_options "name required* switch!" $*
#
# 引数:
#   1:  コマンドライン引数定義( "" でまとめる想定)
#       終端 # で必須
#       終端 ! でスイッチ
#   2*: コマンドライン引数実体
function common::parse_options
{
	# コマンド一覧
	local DEF_ITEMS="$1"

	declare -A REQUIRE_MAP=()
	declare -A SWITCH_MAP=()

	for ITEM in $DEF_ITEMS
	do
		#FIRST=${ITEM:0:1}
		local LAST=${ITEM: -1}

		local REQUIRE=false
		local SWITCH=false
		local KEY=

		if [[ "$LAST" = "#" ]] ; then
			REQUIRE=true
			KEY=${ITEM:0:-1}
		elif [[ "$LAST" = "!" ]] ; then
			SWITCH=true
			KEY=${ITEM:0:-1}
		else
			KEY="${ITEM}"
		fi

		REQUIRE_MAP[$KEY]=$REQUIRE
		SWITCH_MAP[$KEY]=$SWITCH
	done

	# やけくそ処理始まります!
	shift

	while [ $# -gt 0 ] ; do
		local VALUE=$1

		local IS_KEY=false
		local KEY=
		if [[ $VALUE = "--"* ]] ; then
			KEY=${VALUE:2}
			IS_KEY=true
		elif [[ $VALUE = "-"* ]] ; then
			KEY=${VALUE:1}
			IS_KEY=true
		fi

		if $IS_KEY ; then
			local OLD_SETTING_U=${-//[^u]/}
			set +u
			if [[ ${SWITCH_MAP[${KEY}]} = true ]]; then
				_COMMON_OPTIONS[${KEY}]=true
				shift
				continue
			fi
			if [[ -n "${OLD_SETTING_U}" ]] ; then
				set -u
			fi

			shift;

			if [ $# -eq 0 ] ; then
				logger::error "パラメータ指定不備あり" >&2
				exit 10
			fi

			VALUE=$1
			_COMMON_OPTIONS[${KEY}]=$VALUE
		fi

		shift;
	done

	for KEY in "${!REQUIRE_MAP[@]}" ; do
		local VALUE=${REQUIRE_MAP[$KEY]}
		if $VALUE ; then
			local OLD_SETTING_U=${-//[^u]/}
			set +u
			if [[ -z ${_COMMON_OPTIONS[${KEY}]} ]] ; then
				logger::error "必須パラメータ未指定: $KEY"
				exit 20
			fi
			set -u
			if [[ -n "${OLD_SETTING_U}" ]] ; then
				set -u
			fi
		fi
	done
}

# コマンドライン引数に対象のオプションが存在するか
#
# 以下の使用を想定している
# if common::exists_option 'option' ; then ... 存在する
# if ! common::exists_option 'option' ; then ... 存在しない
#
# 引数:
#   1:  コマンドライン引数オプション
#
# 戻り値:
#   0: 存在する
#   !0: 存在しない
function common::exists_option
{
	local NAME="$1"
	local RESULT=0

	local OLD_SETTING_U=${-//[^u]/}
	set +u
	if [[ -z ${_COMMON_OPTIONS[${NAME}]} ]] ; then
		RESULT=1
	fi
	if [[ -n "${OLD_SETTING_U}" ]] ; then
		set -u
	fi

	return ${RESULT}
}


# コマンドライン引数のオプションに対して値を取得
#
# 以下の使用を想定している
# VALUE=$(common::get_option_value, 'option')
#
# 引数:
#   1:  コマンドライン引数オプション
#
# 戻り値:
#   0: 存在する
#   !0: 存在しない
function common::get_option_value
{
	local NAME="$1"

	if ! common::exists_option "${NAME}" ; then
		logger::error "存在しないオプション: ${NAME}"
		return 10
	fi

	local VALUE=${_COMMON_OPTIONS[${NAME}]}
	if [[ "${VALUE}" = true ]] ; then
		logger::error "オプション種別はスイッチ: ${NAME}"
		return 20
	fi

	echo "${VALUE}"
	return 0
}

# common::download_phar_if_not_exists の処理結果
#
# NONE: 実施しなかった
# DOWNLOAD: 実施した
COMMON_DOWNLOAD_PHAR_RESULT=NONE
# Phar ファイルが存在しなければダウンロードして古い Phar ファイル を破棄する
#
# 引数:
#   1: ファイルパス
#   2: ファイルベース名
#   3: URL
#
# 結果:
#   $COMMON_DOWNLOAD_PHAR_RESULT を参照
#   NONE: 実施しなかった
#   DOWNLOAD: 実施した
function common::download_phar_if_not_exists
{
	local FILE_PATH=${1}
	local BASE_NAME=${2}
	local DOWNLOAD_URL=${3}

	if [ ! -f "${FILE_PATH}" ] ; then
		logger::info "DONWLOAD: ${BASE_NAME} ${DOWNLOAD_URL}"

		rm --force "${BASE_NAME}".*
		curl --output "${FILE_PATH}" --location "${DOWNLOAD_URL}"

		COMMON_DOWNLOAD_PHAR_RESULT=DOWNLOAD
	else
		#shellcheck disable=SC2034
		COMMON_DOWNLOAD_PHAR_RESULT=NONE
	fi
}
