<?php

declare(strict_types=1);

/**
 * PUT に対するリクエスト Content-Type, リクエスト本文の返却
 */

if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
	http_response_code(405);
	throw new Exception($_SERVER['REQUEST_METHOD']);
}

$contentType = getallheaders()['Content-Type'];
header("Content-Type: $contentType");

$content = file_get_contents('php://input');
echo $content;
