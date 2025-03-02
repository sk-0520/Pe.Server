# PeServer\Core

Webアプリのコア部分。

## 指針

* PHPの関数とかをラップして再利用する
  * PHP関数がエラー戻り値(false)を返すようなものは例外をぶん投げる
  * 同じような処理でもラップするのです
* Webアプリ簡易ライブラリとして位置するつくり
  * composer は使用しない
    * お勉強もかねている
  * `PeServer\App` は `PeServer\Core` に依存しており、その逆はない

## ライセンス

### Core

* WTFPL

### 依存ライブラリ

* PHP
  * `highlight.php`
    * BSD 3-Clause License
    * https://github.com/scrivo/highlight.php
  * `PHPMailer`
    * LGPL 2.1
    * https://github.com/PHPMailer/PHPMailer/
  * `php-markdown`
    * https://michelf.ca/projects/php-markdown/
  * `smarty`
    * LGPL 3
    * https://www.smarty.net/
  * `whoops`
    * MIT
    * https://github.com/filp/whoops
* font
  * `migmix`
    * IPAフォントライセンスv1.0
    * https://mix-mplus-ipa.osdn.jp/migmix/
