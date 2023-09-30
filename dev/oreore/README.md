# 独自処理用

* 各種バッチファイルは `env.bat` を読み込み、 さらに `@env.bat` が存在する場合に `@env.bat` を読み込む
* `@env.bat` に各種環境情報を記述する(PHPのパスとか ※わたくしあんまりPATH通さないのです)
* 同一プロンプトでは `%OREORE_ENV%` が定義されている間は再読み込みを行わない

## @env.bat

```bat
cd /d %~dp0

set PATH=C:\Applications\xampp\xampp-portable-windows-x64-8.2.0-0-VS16\xampp\php;%PATH%

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

## launch.json

いっつも忘れる。

```json
{
	// IntelliSense を使用して利用可能な属性を学べます。
	// 既存の属性の説明をホバーして表示します。
	// 詳細情報は次を確認してください: https://go.microsoft.com/fwlink/?linkid=830387
	"version": "0.2.0",
	"configurations": [
		{
			"name": "サーバー",
			"type": "php",
			"request": "launch",
			"port": 9003,
			"ignore": [
				"**/vendor/**/*.php",
				"**/Libs/**/*.php"
			],
			"xdebugSettings": {
				"max_children": 128,
				"max_data": 1024,
				"max_depth": 10
			}
		},
		{
			"name": "スクリプト",
			"type": "php",
			"request": "launch",
			"runtimeExecutable": "C:\\Applications\\xampp\\xampp-portable-windows-x64-8.2.0-0-VS16\\xampp\\php\\php.exe",
			"program": "${workspaceFolder}\\dev\\phppad.php",
			"cwd": "${workspaceFolder}",
			"port": 0,
			"runtimeArgs": [
				"-dxdebug.start_with_request=yes"
			],
			"env": {
				"XDEBUG_MODE": "debug,develop",
				"XDEBUG_CONFIG": "client_port=${port}"
			}
		},
	]
}

```

## extract.bat

PHPUnit を Phar で運用しているがさすがに VSCode は呼んでくれないので 
Phar を展開してとりあえず VSCode が認識してくれるようにする展開処理。
