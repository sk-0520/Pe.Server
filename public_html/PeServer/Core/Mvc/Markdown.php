<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Html\CodeHighlighter;

require_once(__DIR__ . '/../../Core/Libs/php-markdown/Michelf/MarkdownExtra.inc.php');

class Markdown
{
	/**
	 * Markdown
	 *
	 * @var \Michelf\Markdown|\Michelf\MarkdownExtra
	 */
	private \Michelf\Markdown $parser;

	private bool $isSafeMode = true; // @phpstan-ignore-line

	/** @SuppressWarnings(PHPMD.MissingImport) */
	public function __construct()
	{
		$this->parser = new \Michelf\MarkdownExtra();
		$this->parser->code_block_content_func = function ($code, $language) {
			$codeHighlighter = new CodeHighlighter();
			return $codeHighlighter->toHtml($language, $code);
		};
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
