<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * まいむ。
 */
abstract class Mime
{
	public const TEXT = 'text/plain';
	public const HTML = 'text/html';
	public const JSON = 'application/json';
	public const GZ = 'application/gzip';
	public const STREAM = 'application/octet-stream';
}
