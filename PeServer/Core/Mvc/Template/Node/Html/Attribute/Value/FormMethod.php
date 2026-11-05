<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum FormMethod: string
{
	case Get = 'get';
	case Post = 'post';
	case Dialog = 'dialog';
}
