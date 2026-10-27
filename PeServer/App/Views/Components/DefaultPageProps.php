<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Bcp47;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\Html\CssSourceNode;
use PeServer\Core\Mvc\Template\Node\Html\ScriptSourceNode;
use PeServer\Core\Mvc\Template\Node\INode;

readonly class DefaultPageProps extends Props
{
	public function __construct(
		public Bcp47 $lang = Bcp47::Japanese,
		public string|null $title = null,
		public INode|null $customHeadNode = null,
		public INode|null $customTailNode = null,
		public ScriptSourceNode|null $customScriptCode = null,
		public CssSourceNode|null $customStyleCode = null,
		public string|null $defaultStylePath = null,
		public string|null $defaultScriptPath = null,
		public INode|null $children = null,
	) {
		//NOP
	}
}
