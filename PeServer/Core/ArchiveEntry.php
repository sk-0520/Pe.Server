<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\Throws;

/**
 * ファイルパスとアーカイブファイル内パス。
 */
readonly class ArchiveEntry
{
	/**
	 * 生成。
	 *
	 * @param non-empty-string $path
	 * @param non-empty-string $entry
	 */
	public function __construct(
		public string $path,
		public string $entry
	) {
		Throws::throwIfNullOrWhiteSpace($path, '$path', ArgumentException::class); // @phpstan-ignore staticMethod.alreadyNarrowedType
		Throws::throwIfNullOrWhiteSpace($entry, '$entry', ArgumentException::class); // @phpstan-ignore staticMethod.alreadyNarrowedType
	}
}
