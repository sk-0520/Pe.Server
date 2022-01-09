<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use \DateTime;
use \DateInterval;
use \DateTimeZone;
use PeServer\Core\FileUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Cryptography;
use PeServer\Core\StringUtility;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Throws\CoreException;
use PeServer\Core\Throws\CryptoException;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ArgumentNullException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Utc;

/**
 * 一時データ管理処理。
 *
 * CookieStore を使ってセッションではない何かを扱う。
 *
 * アプリケーション側で明示的に使用しない想定。
 */
class TemporaryStore
{
	private const ID_LENGTH = 40;

	private TemporaryOption $option;
	private CookieStore $cookie;

	/**
	 * 一時データ。
	 *
	 * @var array<string,mixed>
	 */
	private array $values = array();

	/**
	 * 破棄データ。
	 *
	 * @var string[]
	 */
	private array $removes = array();

	/**
	 * 取り込み処理が行われたか。
	 *
	 * @var boolean
	 */
	private bool $isImported = false;

	public function __construct(TemporaryOption $option, CookieStore $cookie)
	{
		if (StringUtility::isNullOrWhiteSpace($option->name)) {
			throw new ArgumentException('$option->name');
		}
		if (StringUtility::isNullOrWhiteSpace($option->savePath)) {
			throw new ArgumentException('$option->savePath');
		}
		if (is_null($option->cookie->span)) {
			throw new ArgumentNullException('$option->cookie->span');
		}

		$this->option = $option;
		$this->cookie = $cookie;
	}

	private function hasId(): bool
	{
		if ($this->cookie->tryGet($this->option->name, $nameValue)) {
			if (!StringUtility::isNullOrWhiteSpace($nameValue)) {
				return true;
			}
		}

		return false;
	}

	private function getOrCreateId(): string
	{
		if ($this->hasId()) {
			return $this->cookie->getOr($this->option->name, '');
		}

		$bytes = Cryptography::generateRandomBytes(self::ID_LENGTH);

		return $bytes->toHex();
	}

	public function apply(): void
	{
		$id = $this->getOrCreateId();

		$path = $this->getFilePath($id);

		$this->import($id);

		foreach ($this->removes as $key) {
			unset($this->values[$key]);
		}

		if (ArrayUtility::getCount($this->values)) {
			$this->cookie->set($this->option->name, $id, $this->option->cookie);

			FileUtility::createParentDirectoryIfNotExists($path);
			FileUtility::writeJsonFile($path, [
				'timestamp' => Utc::createString(),
				'values' => $this->values
			]);
		} else {
			$this->cookie->remove($this->option->name);

			if (is_file($path)) {
				unlink($path);
			}
		}
	}

	private function getFilePath(string $id): string
	{
		$path = FileUtility::joinPath($this->option->savePath, "$id.json");
		return $path;
	}

	private function import(string $id): void
	{
		if ($this->isImported) {
			return;
		}

		$this->isImported = true;

		$path = $this->getFilePath($id);
		if (!is_file($path)) {
			return;
		}

		/** @var array<string,mixed> */
		$json = FileUtility::readJsonFile($path);

		$timestamp = ArrayUtility::getOr($json, 'timestamp', '');
		if (StringUtility::isNullOrWhiteSpace($timestamp)) {
			return;
		}
		if (!Utc::tryParse($timestamp, $datetime)) {
			return;
		}
		/** @var \DateInterval */
		$span = $this->option->cookie->span;
		$saveTimestamp = $datetime->add($span);
		$currentTimestamp = Utc::create();

		if ($saveTimestamp < $currentTimestamp) {
			return;
		}

		$this->values = array_replace(ArrayUtility::getOr($json, 'values', []), $this->values);
	}

	/**
	 * 追加。
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function push(string $key, mixed $value): void
	{
		$this->values[$key] = $value;

		if (ArrayUtility::contains($this->removes, $key)) {
			$index = array_search($key, $this->removes);
			if ($index === false) {
				throw new InvalidOperationException();
			}
			unset($this->removes[$index]);
		}
	}


	public function peek(string $key): mixed
	{
		$id = $this->getOrCreateId();
		$this->import($id);

		if (!ArrayUtility::existsKey($this->values, $key)) {
			return null;
		}

		return $this->values[$key];
	}

	public function pop(string $key): mixed
	{
		$value = $this->peek($key);

		$this->removes[] = $key;

		return $value;
	}

	public function remove(string $key): void
	{
		unset($this->values[$key]);
		$this->removes[] = $key;
	}
}
