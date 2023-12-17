<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use Closure;
use PeServer\Core\Binary;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Mail\SendMode;

interface IDumpSetting
{
	public function isDryRun(): bool;

	/**
	 * メール送信の送信前までのデータ一覧。
	 * @param array<mixed> $parameters
	 */
	public function dump(array $parameters): void;
}
