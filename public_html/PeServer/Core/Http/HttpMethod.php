<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\Collection\Arr;
use PeServer\Core\Text;

/**
 * HTTPメソッド。
 */
enum HttpMethod: string
{
	case Get = 'GET';
	case Head = 'HEAD';
	case Post = 'POST';
	case Put = 'PUT';
	case Delete = 'DELETE';
	case Connect = 'CONNECT';
	case Options = 'OPTIONS';
	case Trace = 'TRACE';
	case Patch = 'PATCH';

	/**
	 * 通常のGET的なやつ。
	 *
	 * @return self[] Get/Head。
	 */
	public static function gets(): array
	{
		return [self::Get, self::Head];
	}
}
