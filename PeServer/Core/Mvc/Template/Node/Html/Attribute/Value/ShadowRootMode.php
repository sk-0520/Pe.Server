<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum ShadowRootMode: string
{
	case Open = 'open';
	case Closed = 'closed';
}
