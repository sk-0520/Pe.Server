<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Command: string
{
	case ShowModal = 'show-modal';
	case Close = 'close';
	case RequestClose = 'request-close';
	case ShowPopover = 'show-popover';
	case HidePopover = 'hide-popover';
	case TogglePopover = 'toggle-popover';
}
