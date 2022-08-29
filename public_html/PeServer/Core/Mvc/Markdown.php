<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\DefaultValue;
use PeServer\Core\Html\CodeHighlighter;
use PeServer\Core\Regex;
use PeServer\Core\Text;

//require_once(__DIR__ . '/../../Core/Libs/php-markdown/Michelf/MarkdownExtra.inc.php');

class Markdown
{
	#region variable

	/**
	 * Markdown
	 *
	 * @var \Michelf\Markdown|\Michelf\MarkdownExtra
	 */
	private \Michelf\Markdown $parser;

	private bool $isSafeMode = true; // @phpstan-ignore-line

	#endregion

	/** @SuppressWarnings(PHPMD.MissingImport) */
	public function __construct()
	{
		$this->parser = new \Michelf\MarkdownExtra();
		$this->parser->code_block_content_func = function ($code, $language) {
			$codeHighlighter = new CodeHighlighter();
			return '<!-- {CODE -->' . $codeHighlighter->toHtml($language, $code) . '<!-- CODE} -->';
		};
	}

	#region function

	public function setSafeMode(bool $isSafeMode): void
	{
		$this->isSafeMode = $isSafeMode;
	}

	public function build(string $markdown): string
	{
		$html = $this->parser->transform($markdown);

		$regex = new Regex();
		$trimmedHead = $regex->replace(
			$html,
			'/<pre><code(\s+class=".+")?><!-- {CODE -->/',
			DefaultValue::EMPTY_STRING
		);
		$trimmedTail = Text::replace(
			$trimmedHead,
			'</code></pre><!-- CODE} -->',
			DefaultValue::EMPTY_STRING
		);

		return $trimmedTail;
	}

	#endregion
}
