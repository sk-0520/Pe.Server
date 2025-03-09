<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use DateInterval;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Cryptography;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\TemporaryOptions;
use PeServer\Core\Text;
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
	#region define

	private const ID_LENGTH = 40;

	#endregion

	#region variable

	/**
	 * 一時データ。
	 *
	 * @var array<string,mixed>
	 * @phpstan-var array<string,globa-alias-server-store-value>
	 */
	private array $values = [];

	/**
	 * 破棄データ。
	 *
	 * @var string[]
	 */
	private array $removes = [];

	/**
	 * 取り込み処理が行われたか。
	 */
	private bool $isImported = false;

	#endregion

	public function __construct(
		private readonly TemporaryOptions $options,
		private readonly CookieStore $cookie
	) {
		if (Text::isNullOrWhiteSpace($options->name)) {
			throw new ArgumentException('$options->name');
		}
		if (Text::isNullOrWhiteSpace($options->savePath)) {
			throw new ArgumentException('$options->savePath');
		}
		// ArgumentNullException::throwIfNull($options->cookie->span, '$options->cookie->span');
	}

	#region function

	private function hasId(): bool
	{
		if ($this->cookie->tryGet($this->options->name, $nameValue)) {
			if (!Text::isNullOrWhiteSpace($nameValue)) {
				return true;
			}
		}

		return false;
	}

	private function getOrCreateId(): string
	{
		if ($this->hasId()) {
			return $this->cookie->getOr($this->options->name, Text::EMPTY);
		}

		return Cryptography::generateRandomString(self::ID_LENGTH, Cryptography::FILE_RANDOM_STRING);
	}

	public function apply(): void
	{
		$id = $this->getOrCreateId();

		$path = $this->getFilePath($id);

		$this->import($id);

		foreach ($this->removes as $key) {
			unset($this->values[$key]);
		}

		if (Arr::getCount($this->values)) {
			$this->cookie->set($this->options->name, $id, $this->options->cookie);

			Directory::createParentDirectoryIfNotExists($path);
			File::writeJsonFile($path, [
				'timestamp' => Utc::createString(),
				'values' => $this->values
			]);
		} else {
			$this->cookie->remove($this->options->name);

			if (File::exists($path)) {
				File::removeFile($path);
			}
		}
	}

	private function getFilePath(string $id): string
	{
		$path = Path::combine($this->options->savePath, "$id.json");
		return $path;
	}

	private function import(string $id): void
	{
		if ($this->isImported) {
			return;
		}

		$this->isImported = true;

		$path = $this->getFilePath($id);
		if (!File::exists($path)) {
			return;
		}

		/** @var array<string,mixed> */
		$json = File::readJsonFile($path);

		/** @var string */
		$timestamp = $json['timestamp'] ?? Text::EMPTY;
		if (Text::isNullOrWhiteSpace($timestamp)) {
			return;
		}
		if (!Utc::tryParse($timestamp, $datetime)) {
			return;
		}
		/** @var \DateInterval|null */
		$span = $this->options->cookie->span;
		if ($span === null) {
			return;
		}
		$saveTimestamp = $datetime->add($span);
		$currentTimestamp = Utc::create();

		if ($saveTimestamp < $currentTimestamp) {
			return;
		}

		/** @var array<string,mixed> */
		$values = $json['values'] ?? [];
		$this->values = array_replace($values, $this->values);
	}

	/**
	 * 追加。
	 *
	 * @param string $key
	 * @param mixed $value
	 * @phpstan-param globa-alias-server-store-value $value
	 * @return void
	 */
	public function push(string $key, mixed $value): void
	{
		$this->values[$key] = $value;

		if (Arr::containsValue($this->removes, $key)) {
			$index = array_search($key, $this->removes);
			if ($index === false) {
				throw new InvalidOperationException();
			}
			unset($this->removes[$index]);
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param string $key
	 * @return mixed
	 * @phpstan-return globa-alias-server-store-value
	 */
	public function peek(string $key): mixed
	{
		$id = $this->getOrCreateId();
		$this->import($id);

		if (!Arr::containsKey($this->values, $key)) {
			return null;
		}

		return $this->values[$key];
	}

	/**
	 * Undocumented function
	 *
	 * @param string $key
	 * @return mixed
	 * @phpstan-return globa-alias-server-store-value
	 */
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

	#endregion
}
