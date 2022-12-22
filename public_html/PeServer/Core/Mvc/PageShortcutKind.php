<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

enum PageShortcutKind: string
{
	case Normal = 'normal';
	case Short = 'short';
	case Long = 'long';
}
