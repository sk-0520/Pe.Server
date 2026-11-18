#!/bin/bash

function get_git_branch() {
  git branch 2>/dev/null | sed -e '/^[^*]/d' -e 's/* \(.*\)/ (\1)/'
}

PS1="\[\e[32m\]\u\e[0m\]:\e[34m\]\w\[\e[33m\]\$(get_git_branch)\[\e[0m\]\$ "

alias ll='ls -lh --color=auto'
alias code='./dev/code.sh'
alias test='./dev/test.sh'
