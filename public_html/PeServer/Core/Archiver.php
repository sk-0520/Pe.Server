<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArchiveException;

abstract class Archiver
{
	public static function toGzip(Bytes|string $data, int $level = -1, int $encoding = FORCE_GZIP): Bytes
	{
		if ($data instanceof Bytes) {
			$data = $data->getRaw();
		}

		$result = gzencode($data, $level, $encoding);
		if ($result === false) {
			throw new ArchiveException();
		}

		return new Bytes($result);
	}
}
