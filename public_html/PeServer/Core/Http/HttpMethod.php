<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;

/**
 * HTTPメソッド。
 */
enum HttpMethod: string
{
	case Get = 'GET';
	case Post = 'POST';
	case Put = 'PUT';
	case Delete = 'DELETE';
	case Head = 'HEAD';
	case Options = 'OPTIONS';
	case Patch = 'PATCH';
	case Trace = 'TRACE';
}
