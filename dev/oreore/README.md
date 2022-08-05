# 独自処理用

* 各種バッチファイルは `env.bat` を読み込み、 さらに `@env.bat` が存在する場合に `@env.bat` を読み込む
* `@env.bat` に各種環境情報を記述する(PHPのパスとか)
* 同一プロンプトでは `%OREORE_ENV%` が定義されている間は再読み込みを行わない

## @env.bat

```bat
cd /d %~dp0

set PATH=C:\Applications\xampp\xampp-portable-windows-x64-8.0.7-0-VS16\xampp\php;%PATH%

set TEMP=X:\00_others\00_others
set TMP=%TEMP%

set DIR_NAME=.pdepend
set PDEPEND=%TEMP%\%DIR_NAME%
echo %PDEPEND%
if not exist %PDEPEND% (
 mklink /J %USERPROFILE%\%DIR_NAME% %PDEPEND%
)
set COVERAGE_CACHE=%TEMP%\phpunit
```
