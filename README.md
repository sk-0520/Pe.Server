# Pe サーバー処理系

https://peserver.gq/

可能な限り GAS でやってるけど無理なもんは無理。

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
| `PUBLIC` | 公開範囲が `public` で実際問題コメント指定したデータ以外が入ってくるようなもの(配列とか顕著)。可能な限り UT で通過しておきたいところ |
| `TIME` | 対処するには手間がかかる。時間は大事 |
