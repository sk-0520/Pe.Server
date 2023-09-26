<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Text;

/**
 * メールアドレス管理。
 *
 * @immutable
 */
class EmailAddress
{
	#region variable

	/**
	 * 名前。
	 *
	 * @var string
	 */
	public string $name;

	#endregion

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
		if (Text::isNullOrWhiteSpace($name)) {
			$this->name = '';
		} else {
			$this->name = $name;
		}
	}
}
