<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

class Element extends NodeBase
{
	/**
	 *
	 * @param non-empty-string $tagName
	 * @param AttributesBase $attributes
	 * @param Content $content
	 * @param ElementOptions $options
	 */
	public function __construct(
		public readonly string $tagName,
		public AttributesBase $attributes,
		public Content $content,
		public readonly ElementOptions $options
	) {
		//NOP
	}

	#region function

	/**
	 * インデント文字列を取得。
	 *
	 * @param non-negative-int $level
	 * @param BuildOptions $buildOptions
	 * @return string
	 */
	protected function getIndent(int $level, BuildOptions $buildOptions): string
	{
		$hasIndent = ($buildOptions->format & BuildOptions::FORMAT_INDENT) === BuildOptions::FORMAT_INDENT;
		return $hasIndent ? Text::repeat($buildOptions->indent, $level) : '';
	}

	protected function needNewline(ElementOptions $options, BuildOptions $buildOptions): bool
	{
		if ($options->isInline) {
			return false;
		}
		return ($buildOptions->format & BuildOptions::FORMAT_NEWLINE) === BuildOptions::FORMAT_NEWLINE;
	}

	#endregion

	#region INode

	public function toString(int $level, BuildOptions $buildOptions): string
	{
		//TODO: とりあえずえいやで作るので必要に応じて後で対応する
		// インデントや改行は面倒

		$hasChildren = !empty($this->content->nodes);

		$result = "<";
		$result .= $this->tagName;

		foreach ($this->attributes->getNames() as $name) {
			$result .= " ";
			$result .= $this->attributes->build($name);
		}


		if ($this->options->selfClosing) {
			$result .= " />";
		} else {
			$result .= ">";

			if ($hasChildren) {
				$childBuffer = '';
				foreach ($this->content->nodes as $child) {
					$childBuffer .= $child->toString($level + 1, $buildOptions);
				}
				$result .= $childBuffer;
			}

			$result .= "</";
			$result .= $this->tagName;
			$result .= ">";
		}

		return $result;
	}

	#endregion
}
