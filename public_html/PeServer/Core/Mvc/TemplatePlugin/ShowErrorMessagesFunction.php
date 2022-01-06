<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;
use \DOMDocument;
use PeServer\Core\Csrf;
use PeServer\Core\I18n;
use \Smarty_Internal_Template;
use PeServer\Core\ArrayUtility;
use PeServer\Core\HtmlDocument;
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
		if (!$this->existsError()) {
			return '';
		}

		$errors = $this->getErrors();

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

		$dom = new HtmlDocument();

		$ulElement = $dom->createElement('ul');
		$ulElement->setClassList($classes);

		foreach ($errors as $key => $values) {
			if ($targetKey !== $key) {
				continue;
			}

			foreach ($values as $value) {
				$liElement = $ulElement->addElement('li');
				$liElement->addClass('error');

				$messageElement = $liElement->addText($value);
			}
		}

		if ($targetKey === Validator::COMMON) {
			$commonElement = $dom->addElement('div');
			$commonElement->setClassList(['common', 'error']);

			$messageElement = $commonElement->addElement('p');
			$messageElement->addText(I18n::message(I18n::COMMON_ERROR_TITLE));

			if ($ulElement->raw->childElementCount) {
				$commonElement->appendChild($ulElement);
			}
		} else {
			$dom->appendChild($ulElement);
		}

		return $dom->build();
	}
}
