<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum AutoCorrect: string
{
	case Default = '';
	case On = 'on';

	case Off = 'off';
}
