<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use PeServer\Core\Mvc\Template\Node\Html\HtmlDeprecated;

enum ScriptType: string
{
	case importmap = 'importmap';
	case module = 'module';
	#[HtmlDeprecated(HtmlDeprecated::REASON_EXPERIMENTAL)]
	case speculationrules = 'speculationrules';
}
