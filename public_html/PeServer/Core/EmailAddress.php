<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * メールアドレス管理。
 */
class EmailAddress
{
	/**
	 * 名前。
	 *
	 * @var string
	 */
	public string $name;

	/**
	 * 生成。
	 *
	 * @param string $address メールアドレス。
	 * @param string|null $name 名前。
	 */
	public function __construct(
		public string $address,
		?string $name = null
	) {
		if (StringUtility::isNullOrEmpty($name)) {
			$this->name = '';
		} else {
			//@phpstan-ignore-next-line isNullOrEmpty
			$this->name = $name;
		}
	}
}
