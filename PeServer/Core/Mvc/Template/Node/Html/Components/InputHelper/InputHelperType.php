<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\InputHelper;

enum InputHelperType: string
{
	case Text = 'text';
	case Url = 'url';
	case Textarea = 'textarea';
}
