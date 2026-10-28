<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Mvc\Template\Node\ComponentBase;
use PeServer\Core\Mvc\Template\Node\RootProps;

/**
 * @template TProps of RootProps
 * @extends ComponentBase<TProps>
 */
abstract class RootHtmlComponentBase extends ComponentBase
{
	/** @template-use HtmlComponentTrait<TProps> */
	use HtmlComponentTrait;
}
