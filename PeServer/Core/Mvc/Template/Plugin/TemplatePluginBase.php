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
	 * @param Template $smarty
	 * @return boolean
	 */
	protected function existsSmartyError(Template $smarty): bool
	{
		// @phpstan-ignore-next-line tpl_vars
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
	 * @param Template $smarty
	 * @return array<string,string[]>
	 */
	protected function getSmartyErrors(Template $smarty): array
	{
		if ($this->existsSmartyError($smarty)) {
			// @phpstan-ignore-next-line
			return $smarty->tpl_vars['errors']->value;
		}

		throw new InvalidOperationException();
	}

	protected function existsSmartyValues(Template $smarty): bool
	{
		// @phpstan-ignore-next-line tpl_vars
		if (!isset($smarty->tpl_vars['values'])) {
			return false;
		}

		return true;
	}

	/**
	 * Undocumented function
	 *
	 * @param Template $smarty
	 * @return array<string,string|string[]|bool|int|object>
	 */
	protected function getSmartyValues(Template $smarty): array
	{
		if ($this->existsSmartyValues($smarty)) {
			// @phpstan-ignore-next-line
			return $smarty->tpl_vars['values']->value;
		}

		throw new InvalidOperationException();
	}

	#endregion
}
