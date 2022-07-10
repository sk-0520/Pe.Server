<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

/**
 * 応答本文出力機。
 */
interface ICallbackContent
{
	/**
	 * 出力処理。
	 *
	 * 自前で echo とかいい感じに。
	 *
	 * @return void
	 */
	public function output(): void;
}
