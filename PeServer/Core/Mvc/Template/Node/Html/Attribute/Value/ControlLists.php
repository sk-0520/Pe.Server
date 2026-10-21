<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum ControlLists: string
{
	case Nodownload = 'nodownload';

	case Noplaybackrate = 'noplaybackrate';

	case Noremoteplayback = 'noremoteplayback';
}
