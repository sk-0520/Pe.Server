@echo off
chcp 65001 > nul
docker run --rm -it dev-www php %*
