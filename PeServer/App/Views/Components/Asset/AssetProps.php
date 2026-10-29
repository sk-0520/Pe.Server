<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Asset;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\NodeContext;
use PeServer\Core\Mvc\Template\Node\Props;

readonly class AssetProps extends Props
{
	public function __construct(
		public string $file,
		public bool|null $autoSize = null,
		public bool|null $include = null,
		public Rel|null $rel = null,
	) {
		//NOP
	}
}
