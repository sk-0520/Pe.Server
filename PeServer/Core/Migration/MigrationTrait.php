<?php

declare(strict_types=1);

namespace PeServer\Core\Migration;

use PeServer\Core\Log\ILoggerFactory;

trait MigrationTrait
{
	public function __construct(int $version, ILoggerFactory $loggerFactory)
	{
		parent::__construct($version, $loggerFactory);
	}
}
