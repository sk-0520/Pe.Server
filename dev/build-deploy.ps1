$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
$currentDirPath = Split-Path -Parent $MyInvocation.MyCommand.Path

$inputFile = Join-Path $currentDirPath '../public_html/deploy/php-deploy-receiver.php'
$outputFile = Join-Path $currentDirPath '../public_html/deploy/-php-deploy-receiver.php'
$sourceDir = Join-Path $currentDirPath '../public_html'

function Remove-File([string] $path) {
	if (Test-Path -Path $path) {
		Remove-Item -Path $path -Force
	}
}

Remove-File $outputFile

$tempContents = Get-Content -Path $inputFile -Encoding UTF8
$lineNumber = $tempContents | Select-String -Pattern '^//AUTO-GEN-CODE$' | Select-Object -ExpandProperty LineNumber
$baseContents = $tempContents[0 .. ($lineNumber - 1)]

$fileSettings = $baseContents | Select-String -Pattern '^//AUTO-GEN-SETTING:FILE:(.*)$'
foreach ($fileSetting in $fileSettings) {
	$targetFile = $fileSetting.ToString().Trim('//AUTO-GEN-SETTING:FILE:');
	$filePath = Join-Path -Path $sourceDir -ChildPath $targetFile
	$fileContents = Get-Content -Path $filePath -Encoding UTF8
	$editContents = $fileContents `
	| Where-Object { $_ -notmatch '^<\?php' } `
	| Where-Object { $_ -notmatch '^\s*$' } `
	| Where-Object { $_ -notmatch '^declare' } `
	| Where-Object { $_ -notmatch '^namespace' } `
	| Where-Object { $_ -notmatch '^use' }
	$baseContents += $editContents
}

$contents = $baseContents `
| Foreach-Object {
	if($_ -match '@param') {
		return $_
	}
	if($_ -notmatch 'function') {
		return $_
	}
	return $_ -replace '[\?\w\|\\]*\s*(\$)', '$'
} `
| Foreach-Object { $_ -replace '(function.+?\))(\s*:.*)', '$1' }

#Set-Content -Path $outputFile -Value $contents -Encoding UTF8
$utf8n = New-Object 'System.Text.UTF8Encoding' -ArgumentList @($false)
[System.IO.File]::WriteAllLines($outputFile, $contents, $utf8n)

