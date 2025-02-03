<?php

declare(strict_types=1);

namespace PeServer\Core\Cli;

enum ParameterKind
{
	case NeedValue;
	case OptionValue;
	case Switch;
}
