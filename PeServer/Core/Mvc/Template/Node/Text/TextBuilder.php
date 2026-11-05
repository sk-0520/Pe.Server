<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Text;

use PeServer\Core\Text;
use PeServer\Core\Mvc\Template\Node\Text\TextNodeOptions;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\NodeBuilder;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\Text\PlainTextNode;
use PeServer\Core\Mvc\Template\Node\Text\NewLineNode;

class TextBuilder extends NodeBuilder
{
	public function __construct(
		private TextNodeOptions $options
	) {
		//NOP
	}

	#region function

	public function text(string $content): Fragment
	{
		$lines = Text::splitLines($content);

		return $this->lines($lines);
	}

	/**
	 * @param string[] $lines
	 */
	public function lines(array $lines): Fragment
	{
		$count = count($lines);
		$nodes = [];
		for ($i = 0; $i < $count; $i++) {
			$nodes[] = new PlainTextNode($lines[$i]);
			if ($i < $count - 1) {
				$nodes[] = new NewLineNode($this->options->newline);
			}
		}
		return new Fragment($nodes);
	}

	/**
	 * @param non-negative-int $count
	 * @return INode
	 */
	public function break(int $count = 1): INode
	{
		if ($count <= 0) {
			return NoneNode::get();
		}

		$breaks = Arr::repeat(new NewLineNode($this->options->newline), $count);
		/** @disregard P1006 */
		return new Fragment($breaks);
	}

	#endregion
}
