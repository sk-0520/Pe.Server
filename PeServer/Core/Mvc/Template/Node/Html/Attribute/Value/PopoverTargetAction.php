<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum PopoverTargetAction: string
{
	case Hide = 'hide';
	case Show = 'show';
	case Toggle = 'toggle';
}
