<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\BuildOptions;

readonly class HtmlBuildOptions extends BuildOptions
{
	/**
	 * @param int-mask-of<BuildOptions::FORMAT_*> $format
	 */
	public function __construct(
		int $format,
		string $indent,
		public string $version
	) {
		parent::__construct($format, $indent);
	}
}
