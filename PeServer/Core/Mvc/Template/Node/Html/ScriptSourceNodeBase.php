<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Regex;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ScriptType;

/**
 * script 要素の中身。
 */
abstract class ScriptSourceNodeBase extends SourceNode
{
	protected function __construct(string $source, ?Regex $regex = null)
	{
		parent::__construct($source, 'script', $regex);
	}

	#region function

	/**
	 * script 要素の type 属性と中身の種別が整合するか。
	 *
	 * @param ScriptType|null $type
	 */
	abstract public function isAllowedType(ScriptType|null $type): bool;

	#endregion
}
