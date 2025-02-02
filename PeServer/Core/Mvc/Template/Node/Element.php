<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\Template\Node\Attributes;

class Element extends NodeBase
{
	/**
	 *
	 * @param non-empty-string $tagName
	 * @param Attributes $attributes
	 * @param Content $content
	 * @param ElementOptions $options
	 */
	public function __construct(
		public readonly string $tagName,
		public Attributes $attributes,
		public Content $content,
		public readonly ElementOptions $options
	) {
		//NOP
	}

	#region function

	protected function toAttributeString(string $key, string|null $value): string
	{
		$result = $key;
		if ($value !== null) {
			$result .= "=\"{$value}\"";
		}

		return $result;
	}

	#endregion

	#region INode

	public function toString(int $level): string
	{
		//TODO: とりあえずえいやで作るので必要に応じて後で対応する

		$hasAttributes = 0 < count($this->attributes->map);
		$hasChildren = !empty($this->content->values);

		$result = "<";
		$result .= $this->tagName;

		if ($hasAttributes) {
			ksort($this->attributes->map);
			foreach ($this->attributes->map as $key => $value) {
				$result .= " ";
				$result .= $this->toAttributeString($key, $value);
			}
		}


		if ($this->options->selfClosing) {
			$result .= " />";
		} else {
			$result .= ">";

			if ($hasChildren) {
				foreach ($this->content->values as $child) {
					$result .= $child->toString($level + 1);
				}
			}

			$result .= "</";
			$result .= $this->tagName;
			$result .= ">";
		}


		return $result;
	}

	#endregion
}
