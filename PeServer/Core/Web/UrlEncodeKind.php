<?php

declare(strict_types=1);

namespace PeServer\Core\Web;

/**
 * URLエンコード方式。
 * @codeCoverageIgnore
 */
enum UrlEncodeKind: int
{
	case Rfc1738 = PHP_QUERY_RFC1738;
	case Rfc3986 = PHP_QUERY_RFC3986;
}
