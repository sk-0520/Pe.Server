<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Shape: string
{
	case Rect = "rect";
	case Circle = "circle";
	case Poly = "poly";
	case Default = "default";
}
