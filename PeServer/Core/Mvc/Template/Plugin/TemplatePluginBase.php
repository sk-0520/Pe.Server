<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use Smarty\Template;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Throws\InvalidOperationException;

abstract class TemplatePluginBase
{
	#region variable

	protected TemplatePluginArgument $argument;

	#endregion

	protected function __construct(TemplatePluginArgument $argument)
	{
		$this->argument = $argument;
	}

	#region function

	/**
	 * エラーが存在するか。
	 *
	 * @param Template $smartyTemplate
	 * @return boolean
	 */
	protected function existsSmartyError(Template $smartyTemplate): bool
	{
		$smarty = $smartyTemplate->getSmarty();

		if (!isset($smarty->tpl_vars['errors'])) {
			return false;
		}

		$errors = $smarty->tpl_vars['errors']->value;
		if (Arr::isNullOrEmpty($errors)) {
			return false;
		}

		return true;
	}

	/**
	 * Undocumented function
	 *
	 * @param Template $smartyTemplate
	 * @return array<string,string[]>
	 */
	protected function getSmartyErrors(Template $smartyTemplate): array
	{
		if ($this->existsSmartyError($smartyTemplate)) {
			$smarty = $smartyTemplate->getSmarty();

			return $smarty->tpl_vars['errors']->value;
		}

		throw new InvalidOperationException();
	}

	protected function existsSmartyValues(Template $smartyTemplate): bool
	{
		$smarty = $smartyTemplate->getSmarty();

		if (!isset($smarty->tpl_vars['values'])) {
			return false;
		}

		return true;
	}

	/**
	 * Undocumented function
	 *
	 * @param Template $smartyTemplate
	 * @return array<string,string|string[]|bool|int|object>
	 */
	protected function getSmartyValues(Template $smartyTemplate): array
	{
		if ($this->existsSmartyValues($smartyTemplate)) {
			$smarty = $smartyTemplate->getSmarty();
			return $smarty->tpl_vars['values']->value;
		}

		throw new InvalidOperationException();
	}

	#endregion
}
