<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum FormTarget: string
{
	case Self = '_self';
	case Blank = '_blank';
	case Parent = '_parent';
	case Top = '_top';
}
