<?php

declare(strict_types=1);

namespace PeServer\Core\TemplateEngine;

use PeServer\Core\Collection\Vector;
use PeServer\Core\TypeUtility;

class TemplateEngine
{
	/**
	 * 読み込み対象ディレクトリパス一覧。
	 *
	 * @var Vector<string>
	 * @phpstan-var Vector<string>
	 */
	public Vector $baseDirectories = Vector::empty(TypeUtility::TYPE_STRING); //new Vector(TypeUtility::TYPE_STRING);
}
