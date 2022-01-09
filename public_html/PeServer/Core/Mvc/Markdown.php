<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

require_once(__DIR__ . '/../../Core/Libs/php-markdown/Michelf/MarkdownExtra.inc.php');

class Markdown
{
	/**
	 * Undocumented variable
	 *
	 * @var \Michelf\Markdown|\Michelf\MarkdownExtra
	 */
	private \Michelf\Markdown $parser;

	public function __construct()
	{
		$this->parser = new \Michelf\MarkdownExtra();
	}

	public function build(string $markdown): string
	{
		return $this->parser->transform($markdown);
	}
}
