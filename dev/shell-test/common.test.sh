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

	local RETURN_CODE
	common::parse_options 'abc|A|B|C' --abc 'D' || RETURN_CODE=$?
	assert::is_failure ${RETURN_CODE}
}

function test_options_value_case_required
{
	common::parse_options 'abc|A|B|C!' --abc 'A'
	assert::is_success $?
	common::parse_options 'abc|A|B|C!' --abc 'B'
	assert::is_success $?
	common::parse_options 'abc|A|B|C!' --abc 'C'
	assert::is_success $?

	local RETURN_CODE_1
	RETURN_CODE_1="$(common::parse_options 'abc|A|B|C!')"
	assert::is_failure "${RETURN_CODE_1}"

	local RETURN_CODE_2
	RETURN_CODE_2="$(common::parse_options 'abc|A|B|C!' --abc 'D')"
	assert::is_failure "${RETURN_CODE_2}"
}

function test_options_value_error
{
	common::parse_options 'abc' --abc ABC
	local RESULT
	RESULT=$(common::get_option_value xyz)
	assert::is_failure $?
}

function test_options_switch
{
	common::parse_options 'abc switch?' --abc ABC --switch
	if common::exists_option switch ; then
		assert::success
	else
		assert::failure
	fi

	local RETURN_CODE
	common::get_option_value switch || RETURN_CODE=$?
	assert::is_failure ${RETURN_CODE}
}

function test_options_switch_error
{
	local RETURN_CODE
	common::parse_options 'switch|a|b!' --switch || RETURN_CODE=$?
	assert::is_failure ${RETURN_CODE}
}

#--------------------------------
assert::test
