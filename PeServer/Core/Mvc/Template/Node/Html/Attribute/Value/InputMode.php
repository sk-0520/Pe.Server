<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum InputMode: string
{
	case None = 'none';
	case Decimal = 'decimal';
	case Email = 'email';
	case Numeric = 'numeric';
	case Search = 'search';
	case Tel = 'tel';
	case Text = 'text';
	case Url = 'url';
}
