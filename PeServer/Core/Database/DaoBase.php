<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\NullLogger;

/**
 * DBアクセス基底処理。
 *
 * こいつを継承してアクセス処理を構築する。
 */
abstract class DaoBase
{
	#region variable

	#endregion

	/**
	 * 生成。
	 *
	 * @param IDatabaseContext $context 接続処理。
	 */
	protected function __construct(
		protected readonly IDatabaseContext $context,
		protected readonly ILogger $logger
	) {
		//NOP
	}
}
