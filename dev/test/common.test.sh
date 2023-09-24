#!/bin/bash -eu

#shellcheck disable=SC2164
cd "$(cd "$(dirname "${0}")"; pwd)"

#shellcheck disable=SC1091
source ../shell/assert.sh

#shellcheck disable=SC1091
source ../shell/common.sh

function test_options_value_success
{
	common::parse_options 'abc' --abc ABC
	local RESULT
	RESULT=$(common::get_option_value abc)
	assert::is_success $?
	assert::equals "ABC" "${RESULT}"
}

# 空文字は現実装ではむり
function test_options_value_white_success
{
	common::parse_options 'abc' --abc ' '
	local RESULT
	RESULT=$(common::get_option_value abc)
	assert::is_success $?
	assert::equals " " "${RESULT}"
}

function test_options_value_case
{
	common::parse_options 'abc|A|B|C' --abc 'A'
	assert::is_success $?
	common::parse_options 'abc|A|B|C' --abc 'B'
	assert::is_success $?
	common::parse_options 'abc|A|B|C' --abc 'C'
	assert::is_success $?
	common::parse_options 'abc|A|B|C'
	assert::is_success $?

	local RETRUN_CODE
	common::parse_options 'abc|A|B|C' --abc 'D' || RETRUN_CODE=$?
	assert::is_failuer ${RETRUN_CODE}
}

function test_options_value_case_required
{
	common::parse_options 'abc|A|B|C!' --abc 'A'
	assert::is_success $?
	common::parse_options 'abc|A|B|C!' --abc 'B'
	assert::is_success $?
	common::parse_options 'abc|A|B|C!' --abc 'C'
	assert::is_success $?

	local RETRUN_CODE_1
	common::parse_options 'abc|A|B|C!' || RETRUN_CODE_1=$?
	assert::is_failuer ${RETRUN_CODE_1}

	local RETRUN_CODE_2
	common::parse_options 'abc|A|B|C!' --abc 'D' || RETRUN_CODE_2=$?
	assert::is_failuer ${RETRUN_CODE_2}
}

function test_options_value_error
{
	common::parse_options 'abc' --abc ABC
	local RESULT
	RESULT=$(common::get_option_value xyz)
	assert::is_failuer $?
}

function test_options_switch
{
	common::parse_options 'abc switch!' --abc ABC --switch
	if common::exists_option switch ; then
		assert::success
	else
		assert::failuer
	fi

	local RETRUN_CODE
	common::get_option_value switch || RETRUN_CODE=$?
	assert::is_failuer ${RETRUN_CODE}
}

function test_options_switch_error
{
	local RETRUN_CODE
	common::parse_options 'switch|a|b!' --switch || RETRUN_CODE=$?
	assert::is_failuer ${RETRUN_CODE}
}

#--------------------------------
assert::test
