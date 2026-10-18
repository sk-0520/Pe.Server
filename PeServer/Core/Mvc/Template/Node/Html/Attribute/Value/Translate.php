<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Translate: string
{
	case Default = '';
	case Yes = 'yes';

	case No = 'no';
}
