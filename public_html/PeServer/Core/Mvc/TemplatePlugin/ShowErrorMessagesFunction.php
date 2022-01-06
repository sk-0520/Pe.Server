<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;
use \DOMDocument;
use PeServer\Core\Csrf;
use PeServer\Core\I18n;
use \Smarty_Internal_Template;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Throws\CoreException;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;

/**
 * エラーメッセージ表示。
 *
 * $params
 *  * key: 対象キー。
 */
class ShowErrorMessagesFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'show_error_messages';
	}

	protected function functionBodyImpl(): string
	{
		// @phpstan-ignore-next-line
		if (!isset($this->smarty->tpl_vars['errors'])) {
			return '';
		}

		/** @var array<string,string[]> */
		$errors = $this->smarty->tpl_vars['errors']->value;
		if (ArrayUtility::isNullOrEmpty($errors)) {
			return '';
		}

		$targetKey = Validator::COMMON;
		$classes = ['errors'];

		if (!isset($this->params['key']) || $this->params['key'] === Validator::COMMON) {
			$classes[] = 'common-error';
		} else {
			$classes[] = 'value-error';
			$targetKey = $this->params['key'];
		}

		if ($targetKey !== Validator::COMMON) {
			if (!isset($errors[$targetKey])) {
				return '';
			}
			if (ArrayUtility::isNullOrEmpty($errors[$targetKey])) {
				return '';
			}
		}

		$dom = new DOMDocument();

		$ulElement = $dom->createElement('ul');
		$ulElement->setAttribute('class', implode(' ', $classes));

		foreach ($errors as $key => $values) {
			if ($targetKey !== $key) {
				continue;
			}

			foreach ($values as $value) {
				$liElement = $dom->createElement('li');
				$liElement->setAttribute('class', 'error');

				$messageElement = $dom->createTextNode($value);

				$liElement->appendChild($messageElement);

				$ulElement->appendChild($liElement);
			}
		}

		if ($targetKey === Validator::COMMON) {
			$commonElement = $dom->createElement('div');
			$commonElement->setAttribute('class', 'common error');
			$dom->appendChild($commonElement);

			$messageElement = $dom->createElement('p');
			$messageElement->appendChild($dom->createTextNode(I18n::message(I18n::COMMON_ERROR_TITLE)));
			$commonElement->appendChild($messageElement);
			if ($ulElement->childElementCount) {
				$commonElement->appendChild($ulElement);
			}

			$dom->appendChild($commonElement);
		} else {
			$dom->appendChild($ulElement);
		}

		$result = $dom->saveHTML();
		if ($result === false) {
			throw new CoreException();
		}

		return $result;
	}
}
