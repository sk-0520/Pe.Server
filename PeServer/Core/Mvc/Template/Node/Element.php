<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributesBase;
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


	#endregion

	#region INode

	public function toString(int $level): string
	{
		//TODO: とりあえずえいやで作るので必要に応じて後で対応する

		$hasChildren = !empty($this->content->values);

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
