<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum ControlsList: string
{
	case NoDownload = 'nodownload';

	case NoPlayBackrate = 'noplaybackrate';

	case NoRemotePlayback = 'noremoteplayback';
}
