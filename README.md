# Pe サーバー処理系

可能な限り Gas でやってるけど無理なもんは無理。


* 7 (もう使わない)
  * https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/7.1.1/
    * `xampp-portable-win32-7.1.1-0-VC14.7z`
    * `mklink /J C:\Applications\xampp\xampp-portable-win32-7.1.1-0-VC14\xampp\htdocs D:\sk\Documents\programming\Pe\Pe.Server\public_html`
* 8 (サーバー側は8.0.3だけどwindowsのxdebugがそれだと動かんのよ)
  * https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.0.7/
    * `xampp-portable-windows-x64-8.0.7-0-VS16.7z`
    * `mklink /J C:\Applications\xampp\xampp-portable-windows-x64-8.0.7-0-VS16\xampp\htdocs D:\sk\Documents\programming\Pe\Pe.Server\public_html`

* node: `v15.14.0`
* npm: `7.7.6`
