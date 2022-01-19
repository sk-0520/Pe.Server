<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * まいむ。
 */
abstract class Mime
{
	const TEXT = 'text/plain';
	const JSON = 'application/json';
	const GZ = 'application/gzip';
	const STREAM = 'application/octet-stream';
}
