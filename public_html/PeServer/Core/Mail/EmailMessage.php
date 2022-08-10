<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Text;
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

	/**
	 * プレーンテキストが有効か。
	 *
	 * @return boolean
	 */
	public function isText(): bool
	{
		return !Text::isNullOrWhiteSpace($this->text);
	}

	/**
	 * プレーンテキストを設定。
	 *
	 * @param string $value
	 * @return void
	 */
	public function setText(string $value): void
	{
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
		if ($this->isText()) {
			//@phpstan-ignore-next-line
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
	 */
	public function isHtml(): bool
	{
		return !Text::isNullOrWhiteSpace($this->html);
	}

	/**
	 * HTMLを設定。
	 *
	 * @param string $value
	 * @return void
	 */
	public function setHtml(string $value): void
	{
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
		if ($this->isHtml()) {
			//@phpstan-ignore-next-line
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
}
