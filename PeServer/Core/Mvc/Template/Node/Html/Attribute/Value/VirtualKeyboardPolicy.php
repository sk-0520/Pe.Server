<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum VirtualKeyboardPolicy: string
{
	case Default = '';
	case Auto = 'auto';

	case Manual = 'manual';
}
