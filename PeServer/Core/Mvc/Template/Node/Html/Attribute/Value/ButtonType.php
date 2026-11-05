<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum ButtonType: string
{
	case Submit = 'submit';
	case Reset = 'reset';
	case Button = 'button';
}
