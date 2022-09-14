<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Binary;
use PeServer\Core\Text;

/**
 * メールアドレス管理。
 *
 * @immutable
 */
class Attachment
{
	public function __construct(
		public string $name,
		public Binary $data,
		public string $mime = ''
	) {
	}
}
