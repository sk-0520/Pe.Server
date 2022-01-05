<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty_Internal_Template;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\Throws\InvalidOperationException;

abstract class TemplatePluginBase
{
	protected TemplatePluginArgument $argument;

	protected function __construct(TemplatePluginArgument $argument)
	{
		$this->argument = $argument;
	}

	/**
	 * エラーが存在するか。
	 *
	 * @param Smarty_Internal_Template $smarty
	 * @return boolean
	 */
	protected function existsError(Smarty_Internal_Template $smarty): bool
	{
		// @phpstan-ignore-next-line
		if (!isset($smarty->tpl_vars['errors'])) {
			return false;
		}

		$errors = $smarty->tpl_vars['errors']->value;
		if (ArrayUtility::isNullOrEmpty($errors)) {
			return false;
		}

		return true;
	}

	/**
	 * Undocumented function
	 *
	 * @param Smarty_Internal_Template $smarty
	 * @return array<string,string[]>
	 */
	protected function getErrors(Smarty_Internal_Template $smarty): array
	{
		if ($this->existsError($smarty)) {
			return $smarty->tpl_vars['errors']->value;
		}

		throw new InvalidOperationException();
	}
}
