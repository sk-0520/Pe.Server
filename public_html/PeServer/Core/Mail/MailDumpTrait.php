<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Collections\Arr;

trait MailDumpTrait
{
	/**
	 *
	 * @var array{file:string,send?:bool}
	 * @readonly
	 */
	private array $dumpOptions;

	public function isDryRun(): bool
	{
		if (Arr::tryGet($this->dumpOptions, 'send', $result)) {
			if (is_bool($result) && $result) {
				return false;
			}
		}

		return true;
	}

	public function dump(array $parameters): void
	{
	}
}
