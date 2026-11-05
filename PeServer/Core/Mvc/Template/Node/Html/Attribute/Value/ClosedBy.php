<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum ClosedBy: string
{
	case None = 'none';
	case Any = 'any';
	case CloseRequest = 'closerequest';
}
