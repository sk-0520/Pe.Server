<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use PeServer\Core\Mvc\Template\Node\Html\HtmlDeprecated;

#[HtmlDeprecated(HtmlDeprecated::REASON_OBSOLETE)]
enum Clear: string
{
	case Left = 'left';
	case Right = 'right';
	case All = 'all';
}
