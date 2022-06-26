<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplateEngine;

use PeServer\Core\ListArray;

class TemplateEngine
{
	/**
	 * 読み込み対象ディレクトリパス一覧。
	 *
	 * @var ListArray<string>
	 * @phpstan-var ListArray<string>
	 */
	public ListArray $baseDirectories = new ListArray();
}
