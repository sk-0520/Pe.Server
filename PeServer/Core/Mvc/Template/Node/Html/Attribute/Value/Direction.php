<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Direction: string
{
	case Auto = 'auto';
	case LeftToRight = 'ltr';
	case RightToLeft = 'rtl';
}
