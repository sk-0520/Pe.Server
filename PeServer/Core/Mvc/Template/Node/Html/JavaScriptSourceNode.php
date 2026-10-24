<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use PeServer\Core\Regex;
use Override;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ScriptType;

/**
 * 実行される JavaScript(クラシックスクリプトまたはモジュール)。
 */
class JavaScriptSourceNode extends ScriptSourceNode
{
	public function __construct(string $source, ?Regex $regex = null)
	{
		parent::__construct($source, $regex);
	}

	#region ScriptSourceNode

	#[Override]
	public function isAllowedType(ScriptType|null $type): bool
	{
		return $type === null || $type === ScriptType::module;
	}

	#endregion
}
