<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArchiveException;

abstract class Archiver
{
	public static function compressGzip(Bytes $data, int $level = -1, int $encoding = FORCE_GZIP): Bytes
	{
		$result = gzencode($data->getRaw(), $level, $encoding);
		if ($result === false) {
			throw new ArchiveException();
		}

		return new Bytes($result);
	}
}
