#!/bin/bash -ue
#
# Bash スクリプトのテスト窓口
#
cd "$(cd "$(dirname "${0}")"; pwd)"

#shellcheck disable=SC1091
source shell/assert.sh

export ASSERT_TESTS_DIR=shell-test
export ASSERT_TESTS_PATTERN="*.test.sh"

assert::tests
