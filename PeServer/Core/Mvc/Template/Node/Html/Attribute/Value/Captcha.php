<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum Captcha: string
{
	case User = 'user';
	case Environment = 'environment';
}
