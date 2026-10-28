<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Asset;

use Override;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;

/**
 * @extends HtmlComponentBase<\PeServer\App\Views\Components\Asset\AssetProps>
 */
class Asset extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<\PeServer\App\Views\Components\Asset\AssetProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{

		throw new \RuntimeException('Not implemented yet.');
	}
}
