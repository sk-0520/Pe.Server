<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum CrossOrigin: string
{
	case Default = '';
	case Anonymous = 'anonymous';

	case UseCredentials = 'use-credentials';
}
