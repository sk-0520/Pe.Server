<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

/**
 * smarty 修正子
 */
interface ITemplateModifier
{
	#region function

	/**
	 * 修正子名取得。
	 *
	 * @return string
	 */
	public function getModifierName(): string;

	/**
	 * 修正子処理出力。
	 *
	 * @param mixed $value
	 * @param mixed ...$params
	 * @return mixed
	 */
	public function modifierBody(mixed $value, mixed ...$params): mixed;

	#endregion
}
