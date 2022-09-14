<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Log\NullLogger;

trait DaoTrait
{
	public function __construct(
		IDatabaseContext $context
	) {
		parent::__construct($context);
	}
}
