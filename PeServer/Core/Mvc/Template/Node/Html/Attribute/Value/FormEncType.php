<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

enum FormEncType: string
{
	case ApplicationXWwwFormUrlencoded = 'application/x-www-form-urlencoded';
	case MultipartFormData = 'multipart/form-data';
	case TextPlain = 'text/plain';
}
