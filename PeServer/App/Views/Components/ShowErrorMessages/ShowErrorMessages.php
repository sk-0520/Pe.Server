<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\ShowErrorMessages;

use Override;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;

/**
 * @extends HtmlComponentBase<\PeServer\App\Views\Components\ShowErrorMessages\ShowErrorMessagesProps>
 */
class ShowErrorMessages extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<\PeServer\App\Views\Components\ShowErrorMessages\ShowErrorMessagesProps> */
	use HtmlComponentTrait;

	#region HtmlComponentBase

	#[Override]
	protected function getNode(): INode
	{
		throw new \RuntimeException('Not implemented');
	}

	#endregion
}
