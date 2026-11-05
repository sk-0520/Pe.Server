<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum AutoCapitalize: string
{
	case None = 'none';
	case Off = 'off';

	case On = 'on';
	case Sentences = 'sentences';

	case Words = 'words';

	case Characters = 'characters';
}
