<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum FetchPriority: string
{
	case Auto = 'auto';
	case High = 'high';
	case Low = 'low';
}
