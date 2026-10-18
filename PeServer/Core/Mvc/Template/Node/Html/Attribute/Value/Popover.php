<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Popover: string
{
	case Default = '';
	case Auto = 'auto';

	case Hint = 'hint';

	case Manual = 'manual';
}
