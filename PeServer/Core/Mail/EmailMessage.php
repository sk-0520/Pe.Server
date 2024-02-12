<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * メール本文。
 */
class EmailMessage
{
	/**
	 * 生成。
	 *
	 * @param string|null $text プレーンテキスト。
	 * @param string|null $html HTML。
	 */
	public function __construct(
		private ?string $text = null,
		private ?string $html = null
	) {
	}

	#region function

	/**
	 * プレーンテキストが有効か。
	 *
	 * @return boolean
	 * @phpstan-assert-if-true non-empty-string $this->text
	 */
	public function hasText(): bool
	{
		return !Text::isNullOrWhiteSpace($this->text);
	}

	/**
	 * プレーンテキストを設定。
	 *
	 * @param non-empty-string $value
	 * @return void
	 */
	public function setText(string $value): void
	{
		//@phpstan-ignore-next-line [DOCTYPE]
		if (Text::isNullOrWhiteSpace($value)) {
			throw new ArgumentException('$value');
		}

		$this->text = $value;
	}

	/**
	 * プレーンテキスト取得。
	 *
	 * @return string
	 * @throws InvalidOperationException 未設定。
	 */
	public function getText(): string
	{
		if ($this->hasText()) {
			return $this->text;
		}

		throw new InvalidOperationException();
	}

	/**
	 * プレーンテキストを破棄。
	 *
	 * @return void
	 */
	public function clearText(): void
	{
		$this->text = null;
	}

	/**
	 * HTMLが有効か。
	 *
	 * @return boolean
	 * @phpstan-assert-if-true non-empty-string $this->html
	 */
	public function hasHtml(): bool
	{
		return !Text::isNullOrWhiteSpace($this->html);
	}

	/**
	 * HTMLを設定。
	 *
	 * @param non-empty-string $value
	 * @return void
	 */
	public function setHtml(string $value): void
	{
		//@phpstan-ignore-next-line [DOCTYPE]
		if (Text::isNullOrWhiteSpace($value)) {
			throw new ArgumentException('$value');
		}

		$this->html = $value;
	}

	/**
	 * HTML取得。
	 *
	 * @return string
	 * @throws InvalidOperationException 未設定。
	 */
	public function getHtml(): string
	{
		if ($this->hasHtml()) {
			return $this->html;
		}

		throw new InvalidOperationException();
	}

	/**
	 * HTMLを破棄。
	 *
	 * @return void
	 */
	public function clearHtml(): void
	{
		$this->html = null;
	}

	#endregion
}
