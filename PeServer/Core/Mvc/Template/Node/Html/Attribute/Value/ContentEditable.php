<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum ContentEditable: string
{
	case False = 'false';

	case True = 'true';

	case OnlyPlainText = 'plaintext-only';
}
