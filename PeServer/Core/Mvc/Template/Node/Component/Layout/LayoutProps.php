<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Component\Layout;

use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\Html\Tag;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Throws\NotImplementedException;

readonly class LayoutProps extends Props
{
	/**
	 *
	 * @param null|string $language
	 * @param null|array<string,string> $meta
	 * @param null|string $title
	 * @param null|string $defaultStylePath
	 * @param null|string $defaultScriptPath
	 * @param null|string[] $customStylePaths
	 * @param null|string[] $customScriptPath
	 */
	public function __construct(
		public ?string $language,
		public ?array $meta,
		public ?string $title,
		public ?string $defaultStylePath,
		public ?string $defaultScriptPath,
		public ?array $customStylePaths,
		public ?array $customScriptPath,
	) {
		//NOP
	}
}
