<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

require_once(__DIR__ . DIRECTORY_SEPARATOR . '../Libs/highlight.php/HighlightUtilities/functions.php');

use DomainException;
use Highlight\Highlighter;
//use \HighlightUtilities;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;


/**
 * HTMLとしてのコードハイライト処理。
 */
class CodeHighlighter
{
	#region function

	/**
	 * 文字列から強調行番号を取得。
	 *
	 * @param string $value
	 * @return int[]
	 */
	public function toNumbers(string $value): array
	{
		$lineNumbers = [];

		if (!Text::isNullOrWhiteSpace($value)) {
			$numberStrings = Text::split($value, ',');
			$numberValues = array_filter($numberStrings, fn ($s) => TypeUtility::tryParseInteger(Text::trim($s), $unused));
			$lineNumbers = array_map(fn ($s) => (int)Text::trim($s), $numberValues);
		}

		return $lineNumbers;
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
			$hl = new Highlighter();
			try {
				$highlighted = $hl->highlight($language, $source);
				$lines = \HighlightUtilities\splitCodeIntoArray($highlighted->value);
				if ($lines !== false) {
					return $lines;
				}
			} catch (DomainException) {
				//NOP
			}
		}

		return Text::splitLines($source);
	}

	/**
	 * ソースコードをHTML変換。
	 *
	 * @param string $language
	 * @param string $source
	 * @param int[] $lineNumbers
	 * @return string
	 */
	public function toHtml(string $language, string $source, array $lineNumbers = []): string
	{
		$lines = $this->toLines($language, $source);

		$head = '<pre class="source"><code class="hljs ' . $language . '">' . PHP_EOL;
		$tail = PHP_EOL . '</code></pre>';

		$sourceLines = [];
		foreach ($lines as $key => $line) {
			$number = $key + 1;
			$addClass = '';
			if (Arr::in($lineNumbers, $number)) {
				$addClass = 'strong-line';
			}
			$sourceLine = '<span class="code-line ' . $addClass . '" data-line=' . $number . '>' . $line . '</span>';
			$sourceLines[] = $sourceLine;
		}

		$values = Text::join(PHP_EOL, $sourceLines);

		return $head . $values . $tail;
	}

	#endregion
}
