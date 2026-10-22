<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Align: string
{
	case Left = 'left';
	case Right = 'right';
	case Top = 'top';
	case Bottom = 'bottom';
}
