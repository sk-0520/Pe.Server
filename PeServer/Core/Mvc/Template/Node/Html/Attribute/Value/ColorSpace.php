<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum ColorSpace: string
{
	case LimitedSRGB = 'limited-srgb';
	case DisplayP3 = 'display-p3';
}
