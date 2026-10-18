<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

enum AttributeValueMark: string
{
	case DoubleQuote = '"';
	case SingleQuote = "'";
}
