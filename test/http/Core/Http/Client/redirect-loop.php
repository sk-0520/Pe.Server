<?php

declare(strict_types=1);

/**
 * リクエストパラメータ `redirect` の数値を -1 して自身へリダイレクト処理する
 * `redirect` が 0以下であればリダイレクトしない。
 */

$redirectCount = (int)$_GET['redirect'];

if (0 < $redirectCount) {
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $path;
	$url .= "?redirect=" . ($redirectCount - 1);

	header("Location: $url");
	echo "still";

	exit;
}

echo "GOAL!";
