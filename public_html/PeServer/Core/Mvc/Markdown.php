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

	private bool $isSafeMode = true; // @phpstan-ignore-line

	public function __construct()
	{
		$this->parser = new \Michelf\MarkdownExtra();
	}

	public function setSafeMode(bool $isSafeMode): void
	{
		$this->isSafeMode = $isSafeMode;
	}

	public function build(string $markdown): string
	{
		$a = $this->parser->transform($markdown);
		return $a;
	}
}
