![license](https://img.shields.io/github/license/sk-0520/Pe.Server?style=flat)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/sk-0520/Pe.Server)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=sk-0520_Pe.Server&metric=alert_status)
![Bugs](https://sonarcloud.io/api/project_badges/measure?project=sk-0520_Pe.Server&metric=bugs)
![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=sk-0520_Pe.Server&metric=code_smells)
![Duplicated Lines (%)](https://sonarcloud.io/api/project_badges/measure?project=sk-0520_Pe.Server&metric=duplicated_lines_density)
](https://sonarcloud.io/summary/new_code?id=sk-0520_Pe.Server)

# Pe サーバー処理系

https://pe.content-type-text.org/

可能な限り GAS でやってたけどもう無理なんや。

* 8.2 (サーバー側は 8.2.3)
  * https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.0/
    * `xampp-portable-windows-x64-8.2.0-0-VS16.7z`
    * `mklink /J C:\Applications\xampp\xampp-portable-windows-x64-8.2.0-0-VS16\xampp\htdocs D:\sk\programming\Pe\Pe.Server\public_html`

## PHPStan 抑制メモ

PHPStan 検出を抑制する場合はなるべくコメントを書くように。

説明が面倒 + 頻出するものは以下で記述で抑制する  
※目を慣らすため全て `[...]` で囲う

| コメント | 理由 |
|:-:|:--|
| `TIME` | 対処するには手間がかかる。時間は大事 |
| `INJECT` | DI コンテナ経由で初期化される変数 |
| `DOCTYPE` | ドキュメント的には 0 以上しか受け付けないけど型が int なのでチェックはいるよね系。実際問題コメント指定したデータ以外が入ってくるようなもの(配列とか顕著)。可能な限り UT で通過しておきたいところ |
| `PHP_VERSION` | PHPバージョンとかでなんか挙動が変わった系とかの別に残しててもいいじゃん的な |
| `TYPE_INTERFACE` | 外部の型と合わせるための互換記述 |
| `CODE_READONLY` | 読み取り専用と言いつつあくまでコード上だけの規則 |
