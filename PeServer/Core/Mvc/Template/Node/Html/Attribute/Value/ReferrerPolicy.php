<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum ReferrerPolicy: string
{
	case Default = '';

	case NoReferrer = 'no-referrer';
	case NoReferrerWhenDowngrade = 'no-referrer-when-downgrade';

	case Origin = 'origin';
	case OriginWhenCrossOrigin = 'origin-when-cross-origin';
	case SameOrigin = 'same-origin';

	case StrictOriginWhenCrossOrigin = 'strict-origin-when-cross-origin';

	case Manual = 'unsafe-url';
}
