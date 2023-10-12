<?php

declare(strict_types=1);

namespace PeServer\Core\Throws;

use PeServer\Core\Throws\CoreException;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

class DiContainerException extends CoreException implements ContainerExceptionInterface
{
	use ThrowableTrait;
}
