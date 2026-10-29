<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Asset;

use Override;
use PeServer\Core\DI\Inject;
use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Text;

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
		if (Text::isNullOrWhiteSpace($this->props->file)) {
			return NoneNode::get();
		}

		// $isProduction = $this->context->environment->isProduction();

		throw new \RuntimeException('Not implemented yet.');
	}
}
