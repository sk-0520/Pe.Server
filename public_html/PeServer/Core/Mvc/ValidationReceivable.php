<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

interface ValidationReceivable
{
	/**
	 * 検証結果の失敗受領。
	 *
	 * @param string $key
	 * @param array<string,mixed> $parameters
	 * @return void
	 */
	public function receiveError(string $key, int $kind, array $parameters): void;
}
