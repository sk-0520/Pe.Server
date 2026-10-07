---
name: windows-local-php
description: Windows ローカル開発時の PHP 環境設定に関するスキル。
---

- 開発端末の Windows では PHP は PATH を通していない
  - PATH 通せば通すほど環境がぐちゃぐちゃになるので通していない
- dev/oreore/env.bat を実行することで PHP の PATH が通る
- 単純に PHP を実行するには dev/oreore/php-shell.bat を使用すること
- `php-shell.bat --version` で `php --version` が実行される
