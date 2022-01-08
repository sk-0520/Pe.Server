<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Bytes;
use PeServer\Core\Throws\OutputBufferException;

abstract class OutputBuffer
{
	public static function get(callable $action): Bytes
	{
		if (!ob_start()) {
			throw new OutputBufferException('ob_start');
		}
		try {
			$action();
			$buffer = ob_get_contents();
			if ($buffer === false) {
				throw new OutputBufferException('ob_get_contents'); // @phpstan-ignore-line This throw is overwritten by a different one in the finally block below.
			}
			return new Bytes($buffer);  // @phpstan-ignore-line This throw is overwritten by a different one in the finally block below.
		} finally {
			if (!ob_end_clean()) {
				throw new OutputBufferException('ob_end_clean');  // @phpstan-ignore-line The overwriting throw is on this line.
			}
		}
	}
}
