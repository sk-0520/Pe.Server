<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Loading: string
{
	case Eager = 'eager';
	case Lazy = 'lazy';
}
