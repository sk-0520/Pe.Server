<?php

declare(strict_types=1);

namespace PeServer\Core;

use Error;

/**
 * オートローダー。
 *
 * NOTE: なにがあってもPHP標準関数ですべて処理すること。
 * 調整する場合は継承してお好きに。
 *
 * @phpstan-type NamespacePrefixAlias string
 * @phpstan-type BaseDirectoryAlias non-empty-string
 * @phpstan-type ClassIncludesAlias array<class-string,class-string|non-empty-string>
 * @phpstan-type InputMappingIncludesAlias array{directory:BaseDirectoryAlias,includes?:ClassIncludesAlias|null,extensions?:array<non-empty-string>|null}
 * @phpstan-type EnabledMappingIncludesAlias array{directory:BaseDirectoryAlias,includes:ClassIncludesAlias,extensions:non-empty-array<non-empty-string>}
 */
class AutoLoader
{
	#region variable

	/**
	 * 名前空間接頭辞とプロジェクトマッピング。
	 *
	 * @var array
	 * @phpstan-var array<NamespacePrefixAlias,EnabledMappingIncludesAlias>
	 */
	private $setting = [];

	#endregion

	/**
	 * 生成。
	 *
	 * @param array|null $setting 初期化設定。内部的には `add` が実施される。
	 * @phpstan-param array<NamespacePrefixAlias,InputMappingIncludesAlias>|null $setting
	 */
	public function __construct(?array $setting = null)
	{
		if ($setting !== null) {
			foreach ($setting as $k => $v) {
				$this->setImpl($k, $v, false);
			}
		}

		// Core 固有ライブラリ登録
		$libs = __DIR__ . DIRECTORY_SEPARATOR . 'Libs' . DIRECTORY_SEPARATOR;
		/** @phpstan-var array<NamespacePrefixAlias,InputMappingIncludesAlias> */
		$libraries = [
			// highlight.php
			'Highlight' => [
				'directory' => $libs . 'highlight.php/Highlight',
			],
			// php-markdown
			'Michelf' => [
				'directory' => $libs . 'php-markdown/Michelf',
			],
			// PHPMailer
			'PHPMailer\PHPMailer' => [
				'directory' => $libs . 'PHPMailer/src',
			]
		];
		foreach ($libraries as $k => $v) {
			$this->setImpl($k, $v, false);
		}
	}

	#region function

	/**
	 * 名前空間接頭辞の調整。
	 *
	 * @param string $namespacePrefix
	 * @phpstan-param NamespacePrefixAlias $namespacePrefix
	 * @return string
	 * @phpstan-return NamespacePrefixAlias
	 */
	protected function adjustNamespacePrefix(string $namespacePrefix): string
	{
		return trim($namespacePrefix, "\\ \t") . '\\';
	}

	/**
	 * 基底ディレクトリの調整。
	 *
	 * @param string $path
	 * @phpstan-param BaseDirectoryAlias $path
	 * @return string
	 */
	protected function adjustDirectory(string $path): string
	{
		$trimPath = rtrim($path, "\\/ \t");
		$replacePath = str_replace('\\', DIRECTORY_SEPARATOR, $trimPath);
		if (empty($replacePath) && $replacePath !== '0') {
			return '';
		}

		return $replacePath . DIRECTORY_SEPARATOR;
	}

	/**
	 * クラス名の調整。
	 *
	 * @param string $className
	 * @phpstan-param class-string|string $className
	 * @return string
	 */
	protected function adjustClassName(string $className): string
	{
		return trim($className, "\\ \t");
	}

	/**
	 * マッピング設定。
	 *
	 * @param string $namespacePrefix
	 * @phpstan-param NamespacePrefixAlias $namespacePrefix
	 * @param array $mapping
	 * @phpstan-param InputMappingIncludesAlias $mapping
	 * @param bool $overwrite
	 */
	protected function setImpl(string $namespacePrefix, array $mapping, bool $overwrite): void
	{
		$fixedNamespacePrefixAlias = $this->adjustNamespacePrefix($namespacePrefix);
		if (!$overwrite && isset($this->setting[$fixedNamespacePrefixAlias])) {
			throw new Error('[' . $namespacePrefix . '] overwrite: $namespacePrefix => ' . $fixedNamespacePrefixAlias);
		}

		$fixedDirectoryPath = $this->adjustDirectory($mapping['directory']);
		if (empty($fixedDirectoryPath)) {
			throw new Error('[' . $namespacePrefix . '] empty: $mapping[\'directory\'] => ' . $fixedDirectoryPath);
		}

		/** @phpstan-var ClassIncludesAlias */
		$fixedIncludes = [];
		if (isset($mapping['includes']) && $mapping['includes'] !== null) { //@phpstan-ignore-line non-empty-array !== null
			foreach ($mapping['includes'] as $aliasClassName => $includeClassName) {
				$fixedAliasClassName = $this->adjustClassName($aliasClassName);
				$fixedIncludeClassName = $this->adjustClassName($includeClassName);

				if (isset($fixedIncludes[$fixedAliasClassName])) {
					throw new Error('[' . $namespacePrefix . '] overwrite: $aliasClassName => ' . $fixedAliasClassName);
				}
				if (empty($fixedAliasClassName)) {
					throw new Error('[' . $namespacePrefix . '] empty: $aliasClassName => ' . $fixedAliasClassName);
				}
				if (empty($fixedIncludeClassName)) {
					throw new Error('[' . $namespacePrefix . '] empty: $includeClassName => ' . $fixedIncludeClassName);
				}

				$fixedIncludes[$fixedAliasClassName] = $fixedIncludeClassName;
			}
		}

		$fixedExtensions = [];
		if (isset($mapping['extensions']) && $mapping['extensions'] !== null) { //@phpstan-ignore-line
			foreach ($mapping['extensions'] as $extension) {
				$extension = trim($extension);
				if (!empty($extension)) {
					if ($extension[0] !== '.') {
						$extension = '.' . $extension;
					}
					if (!in_array($extension, $fixedExtensions, true)) {
						$fixedExtensions[] = $extension;
					}
				}
			}
		}
		if (!count($fixedExtensions)) {
			$fixedExtensions = ['.php'];
		}

		/** @phpstan-var EnabledMappingIncludesAlias */
		$enabledMapping = [
			'directory' => $fixedDirectoryPath,
			'includes' => $fixedIncludes,
			'extensions' => $fixedExtensions,
		];
		$this->setting[$fixedNamespacePrefixAlias] = $enabledMapping;
	}

	/**
	 * マッピング設定。
	 *
	 * 既存のマッピングが存在する場合は上書き。
	 *
	 * @param string $namespacePrefix
	 * @phpstan-param NamespacePrefixAlias $namespacePrefix
	 * @param array $mapping
	 * @phpstan-param InputMappingIncludesAlias $mapping
	 */
	final public function set(string $namespacePrefix, array $mapping): void
	{
		$this->setImpl($namespacePrefix, $mapping, true);
	}

	/**
	 * マッピング追加。
	 *
	 * @param string $namespacePrefix
	 * @phpstan-param NamespacePrefixAlias $namespacePrefix
	 * @param array $mapping
	 * @phpstan-param InputMappingIncludesAlias $mapping
	 * @throws Error 既存のマッピングが存在する
	 */
	final public function add(string $namespacePrefix, array $mapping): void
	{
		$this->setImpl($namespacePrefix, $mapping, false);
	}

	/**
	 * マッピング取得。
	 *
	 * @param string $namespacePrefix
	 * @return array<string,mixed>|null 見つからなかった場合は `null` を返す。
	 * @phpstan-return EnabledMappingIncludesAlias|null
	 */
	final public function get(string $namespacePrefix): ?array
	{
		$fixedNamespacePrefix = $this->adjustNamespacePrefix($namespacePrefix);

		if (isset($this->setting[$fixedNamespacePrefix])) {
			return $this->setting[$fixedNamespacePrefix];
		}

		return null;
	}

	/**
	 * 登録。
	 *
	 * `spl_autoload_register` ラッパー。
	 *
	 * @param bool $prepend キューの先頭に登録するか。
	 * @return void
	 * @throws Error 登録ディレクトが存在しない
	 * @see https://www.php.net/manual/function.spl-autoload-register.php
	 */
	public function register(bool $prepend = false): void
	{
		foreach ($this->setting as $namespacePrefix => $mapping) {
			if (!is_dir($mapping['directory'])) {
				throw new Error('[' . $namespacePrefix . '] not found: $mapping[\'directory\'] => ' . $mapping['directory']);
			}
		}

		spl_autoload_register([$this, 'load'], true, $prepend);
	}

	/**
	 * 登録解除。
	 *
	 * `spl_autoload_unregister` ラッパー。
	 *
	 * @return bool 解除できたか。
	 * @see https://www.php.net/manual/function.spl-autoload-unregister.php
	 */
	public function unregister(): bool
	{
		return spl_autoload_unregister([$this, 'load']);
	}

	/**
	 * 読み込み対象ファイルの取得。
	 *
	 * 本処理はエラーとか例外はやっちゃいけない。
	 *
	 * @param string $fullName
	 * @return string|null ファイルが存在する場合はファイルパス。存在しない(担当じゃない)場合は `null` を返す。
	 * @phpstan-return non-empty-string|null ファイルが存在する場合はファイルパス。存在しない(担当じゃない)場合は null を返す。
	 */
	protected function findIncludeFile(string $fullName): ?string
	{
		foreach ($this->setting as $namespacePrefix => $mapping) {
			if (str_starts_with($fullName, $namespacePrefix)) {
				foreach ($mapping['includes'] as $aliasClassName => $includeClassName) {
					if ($aliasClassName === $fullName) {
						$fullName = $includeClassName;
						break;
					}
				}

				$classBaseName = substr($fullName, strlen($namespacePrefix));
				$fileBasePath = str_replace('\\', DIRECTORY_SEPARATOR, $classBaseName);
				$filePathWithoutExtensions = $mapping['directory'] . $fileBasePath;

				foreach ($mapping['extensions'] as $extension) {
					$filePath = $filePathWithoutExtensions . $extension;
					if (is_file($filePath)) {
						return $filePath;
					}
				}
			}
		}

		return null;
	}

	/**
	 * ファイル読み込み処理。
	 *
	 * @param string $fullName 完全名。
	 */
	private function load(string $fullName): void
	{
		$filePath = $this->findIncludeFile($fullName);
		if ($filePath !== null) {
			require $filePath;
		}
	}

	#endregion
}
