<?php

declare(strict_types=1);

namespace PeServer\Core\Mail;

use PeServer\Core\Binary;
use PeServer\Core\Mime;
use PeServer\Core\Text;

/**
 * メール添付データ。
 */
readonly class Attachment
{
	/**
	 * 生成。
	 *
	 * @param non-empty-string $name ファイル名。
	 * @param Binary $data ファイルデータ。
	 * @param string $mime まいむ。
	 * @phpstan-param Mime::*|string $mime
	 * @codeCoverageIgnore
	 */
	public function __construct(
		public string $name,
		public Binary $data,
		public string $mime = ''
	) {
	}
}
