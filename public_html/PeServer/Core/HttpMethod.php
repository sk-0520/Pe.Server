<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * HTTPメソッド。
 */
class HttpMethod
{
	/**
	 * ルーティング登録時に使用するメソッド無関係設定。
	 */
	const ALL = '';

	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';
}
