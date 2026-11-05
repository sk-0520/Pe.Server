<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use Override;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Text;

class HtmlElement extends Element
{
	public function __construct(
		string $tagName,
		HtmlGlobalAttributes $attributes,
		INode $children,
		ElementOptions $options
	) {
		parent::__construct($tagName, $attributes, $children, $options);
	}

	#region Element

	#[Override]
	protected function buildBuildTagName(string $tagName, ElementOptions $options, BuildOptions $buildOptions): string
	{
		if ($buildOptions instanceof HtmlBuildOptions && $buildOptions->version === HtmlBuilder::HTML_VERSION_EMAIL) {
			return Text::toUpper($tagName);
		}

		return parent::buildBuildTagName($tagName, $options, $buildOptions);
	}

	#[Override]
	protected function buildStartTag(string $tagName, string $builtAttributes, ElementOptions $options, BuildOptions $buildOptions): string
	{
		if ($options->selfClosing && $buildOptions instanceof HtmlBuildOptions && $buildOptions->version === HtmlBuilder::HTML_VERSION_EMAIL) {
			return '<' . $tagName . $builtAttributes . '>';
		}

		return parent::buildStartTag($tagName, $builtAttributes, $options, $buildOptions);
	}

	#endregion
}
