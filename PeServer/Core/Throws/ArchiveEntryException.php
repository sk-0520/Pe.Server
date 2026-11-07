<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use Throwable;

class ArchiveEntryException extends ArchiveException
{
	#region define

	const MODE_ADD_FILE = 1;

	#endregion

	public function __construct(
		/** @var self::MODE_* */
		public int $mode,
		string $message = '',
		int $code = 0,
		?Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
	}
}
