<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * `stream_get_meta_data`
 *
 * @immutable
 */
class StreamMetaData
{
	/**
	 * 生成。
	 *
	 * @param bool $isTimedOut
	 * @param bool $isBlocked
	 * @param bool $eof
	 * @param string $streamType
	 * @param string $wrapperType
	 * @param string $mode
	 * @param bool $seekable
	 * @param string $uri
	 * @param array<mixed> $crypto
	 * @param mixed $data
	 * @param int $unreadBytes
	 */
	public function __construct(
		public bool $isTimedOut,
		public bool $isBlocked,
		public bool $eof,
		public string $streamType,
		public string $wrapperType,
		public string $mode,
		public bool $seekable,
		public string $uri,
		public array $crypto,
		public mixed $data,
		public int $unreadBytes
	) {
	}

	/**
	 * `stream_get_meta_data` から作成。
	 *
	 * @param array<string,mixed> $values
	 */
	public static function createFromStream(array $values):self
	{
		return new self(
			ArrayUtility::getOr($values, 'timed_out', false),
			ArrayUtility::getOr($values, 'blocked', false),
			ArrayUtility::getOr($values, 'eof', false),
			(string)$values['stream_type'],
			(string)$values['wrapper_type'],
			(string)$values['mode'],
			(bool)$values['seekable'],
			(string)$values['uri'],
			ArrayUtility::getOr($values, 'crypto', []),
			ArrayUtility::getOr($values, 'wrapper_data', null),
			(int)$values['unread_bytes']
		);
	}
}
