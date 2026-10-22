<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Decoding: string
{
	case Auto = 'auto';
	case Sync = 'sync';
	case Async = 'async';
}
