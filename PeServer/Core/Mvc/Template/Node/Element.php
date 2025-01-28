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
	 * @param INode[] $children
	 * @param Props $props
	 * @param ElementOptions $options
	 */
	public function __construct(
		public readonly string $tagName,
		public Attributes $attributes,
		public array $children,
		public readonly Props $props,
		public readonly ElementOptions $options
	) {
		$this->children = $children;
	}

	#region function

	protected function toAttributeString(string $key, string|null $value): string
	{
		$result = $key;
		if($value !== null) {
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
		$hasChildren = empty($this->children);

		$result = "<";
		$result .= $this->tagName;

		if ($hasAttributes) {
			ksort($this->attributes->map);
			foreach($this->attributes->map as $key => $value) {
				$result .= " ";
				$result .= $this->toAttributeString($key, $value);
			}
		}


		if ($this->options->selfClosing) {
			$result .= " />";
		} else {
			$result .= ">";

			if ($hasChildren) {
				foreach ($this->children as $child) {
					$child->toString($level + 1);
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
