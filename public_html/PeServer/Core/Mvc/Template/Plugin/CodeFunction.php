<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use DomainException;
use PeServer\Core\ArrayUtility;
use PeServer\Core\DefaultValue;
use PeServer\Core\Mvc\Markdown;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Mvc\Template\Plugin\TemplateBlockFunctionBase;
use Highlight\Highlighter;
use HighlightUtilities\Functions;
use PeServer\Core\AutoLoader;
use PeServer\Core\CoreUtility;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;

const LIB_DIR = CoreUtility::LIBRARY_DIRECTORY_PATH . DIRECTORY_SEPARATOR . 'highlight.php/src';
// foreach(Directory::getFiles(CoreUtility::LIBRARY_DIRECTORY_PATH . DIRECTORY_SEPARATOR . 'highlight.php/src/', true) as $file) {
// 	//require_once __DIR__ . "/../../../Libs/highlight.php/src/Highlight/Highlighter.php";
// 	if(Text::endsWith($file, '.php', true)){
// 		require_once $file;
// 	}
// }

(new AutoLoader([
	'Highlight' => [
		'directory' => LIB_DIR //. '/Highlight'
	],
	'HighlightUtilities' => [
		'directory' => LIB_DIR //. '/HighlightUtilities'
	]
]))->register();

class CodeFunction extends TemplateBlockFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'code';
	}

	/**
	 * タグ付けソースを行ごとに返却。
	 *
	 * @param string $language
	 * @param string $source
	 * @return string[]
	 */
	private function toLines(string $language, string $source): array
	{
		if (!Text::isNullOrWhiteSpace($language)) {
			$hl = new Highlighter(); //@phpstan-ignore-line Highlighter
			try {
				$highlighted = $hl->highlight($language, $source); //@phpstan-ignore-line highlight
				$lines = Functions::splitCodeIntoArray($highlighted->value); //@phpstan-ignore-line splitCodeIntoArray
				if ($lines !== false) {
					return $lines;
				}
			} catch (DomainException) {
				//NONE
			}
		}

		return Text::splitLines($source);
	}

	protected function functionBlockBodyImpl(string $content): string
	{
		$language = ArrayUtility::getOr($this->params, 'language', DefaultValue::EMPTY_STRING);
		$numbers = (string)($this->params['numbers'] ?? '');

		$lineNumbers = [];
		if (!Text::isNullOrWhiteSpace($numbers)) {
			$numberStrings = Text::split($numbers, ',');
			$numberValues = array_filter($numberStrings, fn ($s) => TypeUtility::tryParseInteger(Text::trim($s), $unused));
			$lineNumbers = array_map(fn ($s) => (int)Text::trim($s), $numberValues);
		}

		$lines = $this->toLines($language, $content);

		$head = '<pre class="source"><code class="hljs ' . $language . '">' . PHP_EOL;
		$tail = PHP_EOL . '</code></pre>';

		$sourceLines = [];
		foreach ($lines as $key => $line) {
			$number = $key + 1;
			$addClass = '';
			if (ArrayUtility::in($lineNumbers, $number)) {
				$addClass = 'strong-line';
			}
			$sourceLine = '<span class="code-line ' . $addClass . '" data-line=' . $number . '>' . $line . '</span>';
			$sourceLines[] = $sourceLine;
		}

		$values = Text::join(PHP_EOL, $sourceLines);

		return $head . $values . $tail;
	}
}
