<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\I18n;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Text;
use PeServer\Core\Mvc\Logic\Validator;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Mvc\Template\Plugin\TemplateFunctionBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;

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

	#region TemplateFunctionBase

	public function getFunctionName(): string
	{
		return 'show_error_messages';
	}

	protected function functionBodyImpl(): string
	{
		if (!$this->existsError()) {
			return Text::EMPTY;
		}

		$errors = $this->getErrors();

		if (Arr::isNullOrEmpty($errors)) {
			return Text::EMPTY;
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
				return Text::EMPTY;
			}
			if (Arr::isNullOrEmpty($errors[$targetKey])) {
				return Text::EMPTY;
			}
		}

		$dom = new HtmlDocument();

		$ulElement = $dom->createTagElement('ul');
		$ulElement->setClassList($classes);

		foreach ($errors as $key => $values) {
			if ($targetKey !== $key) {
				continue;
			}

			foreach ($values as $value) {
				$liElement = $ulElement->addTagElement('li');
				$liElement->addClass('error');

				$messageElement = $liElement->addText($value);
			}
		}

		if ($targetKey === Validator::COMMON) {
			$commonElement = $dom->addTagElement('div');
			$commonElement->setClassList(['common', 'error']);

			$messageElement = $commonElement->addTagElement('p');
			$messageElement->addText(I18n::message(I18n::COMMON_ERROR_TITLE));

			if ($ulElement->raw->childElementCount) {
				$commonElement->appendChild($ulElement);
			}
		} else {
			$dom->appendChild($ulElement);
		}

		return $dom->build();
	}

	#endregion
}
