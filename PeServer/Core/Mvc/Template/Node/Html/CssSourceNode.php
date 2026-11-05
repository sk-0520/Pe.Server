<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Regex;
/**
 * style 要素の中身(CSS)。
 */
class CssSourceNode extends SourceNode
{
	public function __construct(string $source, ?Regex $regex = null)
	{
		parent::__construct($source, 'style', $regex);
	}
}
