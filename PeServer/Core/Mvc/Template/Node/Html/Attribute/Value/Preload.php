<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Preload: string
{
	case Default = '';
	case Auto = 'auto';

	case None = 'none';

	case Metadata = 'metadata';
}
