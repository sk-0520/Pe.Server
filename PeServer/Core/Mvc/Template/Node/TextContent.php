<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

final class TextContent extends Content
{
	/**
	 *
	 * @param string $text
	 *
	 */
	public function __construct(
		string|TextNode $text
	) {
		parent::__construct([$text instanceof TextNode ? $text : new TextNode($text)]);
	}
}
