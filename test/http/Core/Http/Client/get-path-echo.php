<?php

declare(strict_types=1);

/**
 * GET に対するリクエストURIの返却
 */

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(405);
	throw new Exception($_SERVER['REQUEST_METHOD']);
}

echo $_SERVER['REQUEST_URI'];
