<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use PeServer\Core\Mvc\Template\Node\Html\HtmlDeprecated;

enum Wrap: string
{
	case Hard = 'hard';
	case Soft = 'soft';
	#[HtmlDeprecated(HtmlDeprecated::REASON_NON_STANDARD)]
	case Off = 'off';
}
