#!/bin/bash -ue
# 共通処理まとめない無理だこれ
#
# * 使用側から `source common.sh` で取り込まれることを想定している
# * 二重に読み込まれることは想定していない
#

# コマンドライン引数の辞書変数
# 決まりを守って正しく使う
declare -A COMMON_OPTIONS=()

# コマンドライン引数($COMMON_OPTIONS)を使用可能にする。
#
# オプションは --name value もしくは --switch 形式のみを受け付ける
#
# 以下の使用を想定している
# #shellcheck disable=SC2048,SC2086
# common::parse_options "name required* switch!" $*
#
# 引数:
#   1:  コマンドライン引数定義( "" でまとめる想定)
#       終端 * で必須
#       終端 ! でスイッチ
#   2*: コマンドライン引数実体
function common::parse_options()
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

		if [ "$LAST" = "*" ] ; then
			REQUIRE=true
			KEY=${ITEM:0:-1}
		elif [ "$LAST" = "!" ] ; then
			SWITCH=true
			KEY=${ITEM:0:-1}
		else
			KEY="${ITEM}"
		fi

		# echo " [$ITEM] > $KEY  $REQUIRE  $SWITCH"

		REQUIRE_MAP[$KEY]=$REQUIRE
		SWITCH_MAP[$KEY]=$SWITCH
	done

	# やけくそ処理始まります!
	shift

	while [ $# -gt 0 ] ; do
		VALUE=$1

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
			set +u
			if [[ ${SWITCH_MAP[${KEY}]} = true ]]; then
				COMMON_OPTIONS[${KEY}]=true
				shift
				continue
			fi
			set -u

			shift;

			if [ $# -eq 0 ] ; then
				echo "パラメータ指定不備あり"
				exit 10
			fi

			VALUE=$1
			COMMON_OPTIONS[${KEY}]=$VALUE
		fi

		shift;
	done

	for KEY in "${!REQUIRE_MAP[@]}" ; do
		xxx=${REQUIRE_MAP[$KEY]}
		if $xxx ; then
			set +u
			if [[ -z ${COMMON_OPTIONS[${KEY}]} ]] ; then
				echo "必須パラメータ未指定: $KEY"
				exit 20
			fi
			set -u
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
#   1: 存在しない
function common::exists_option()
{
	local NAME="$1"
	local RESULT=0

	set +u
	if [[ -z ${COMMON_OPTIONS[${NAME}]} ]] ; then
		RESULT=1
	fi
	set -u

	return ${RESULT}
}

declare DOWNLOAD_PHAR_RESULT=NONE
# Phar ファイルが存在しなければダウンロードして古い Phar ファイル を破棄する
#
# 引数:
#   1: ファイルパス
#   2: ファイルベース名
#   3: URL
#
# 結果:
#   $DOWNLOAD_PHAR_RESULT を参照
#   NONE: 実施しなかった
#   DOWNLOAD: 実施した
function common::download_phar_if_not_exists
{
	local FILE_PATH=${1}
	local BASE_NAME=${2}
	local DOWNLOAD_URL=${3}


	if [ ! -f "${FILE_PATH}" ] ; then
		echo "DONWLOAD: ${BASE_NAME} ${DOWNLOAD_URL}"

		rm --force "${BASE_NAME}".*
		curl --output "${FILE_PATH}" --location "${DOWNLOAD_URL}"

		DOWNLOAD_PHAR_RESULT=DOWNLOAD
	else
		#shellcheck disable=SC2034
		DOWNLOAD_PHAR_RESULT=NONE
	fi
}
