<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Attribute;

enum AttributeValueMark: string
{
	case DoubleQuote = '"';
	case SingleQuote = "'";
}
