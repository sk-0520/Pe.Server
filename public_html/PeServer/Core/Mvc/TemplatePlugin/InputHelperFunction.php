<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;
use DOMElement;
use \DOMDocument;
use PeServer\Core\Csrf;
use \Smarty_Internal_Template;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\TypeConverter;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\Mvc\TemplatePlugin\ShowErrorMessagesFunction;

/**
 * 入力要素のヘルパー。
 *
 * $params
 *  * key: 対象キー, valuesと紐づく
 *  * type: 対象のinput[type="*"]かtextareaを指定。不明時は input としてそのまま生成される。radio/checkboxは想定していないのでなんか別の方法を考えた方がいい
 *  * auto_error: true/false 未指定かtrueの場合にエラー表示も自動で行う(show_error_messages関数の内部呼び出し)
 *  * readonly: true/false trueの場合に readonly を設定する
 *  * disabled: true/false trueの場合に disabled を設定する
 */
class InputHelperFunction extends TemplateFunctionBase
{
	private ShowErrorMessagesFunction $showErrorMessagesFunction;

	public function __construct(TemplatePluginArgument $argument, ShowErrorMessagesFunction $showErrorMessagesFunction)
	{
		parent::__construct($argument);

		$this->showErrorMessagesFunction = $showErrorMessagesFunction;
	}

	public function getFunctionName(): string
	{
		return 'input_helper';
	}

	protected function functionBodyImpl(): string
	{
		$targetKey = $this->params['key']; // 必須

		$showAutoError = true;
		if (ArrayUtility::tryGet($this->params, 'auto_error', $autError)) {
			$showAutoError = filter_var($autError, FILTER_VALIDATE_BOOL) && boolval($autError);
		}

		$hasError = false;
		// @phpstan-ignore-next-line
		if (isset($this->smarty->tpl_vars['errors'])) {
			/** @var array<string,string[]> */
			$errors = $this->smarty->tpl_vars['errors']->value;
			foreach ($errors as $key => $values) {
				if ($targetKey === $key) {
					$hasError = 0 < ArrayUtility::getCount($values);
					break;
				}
			}
		}

		$dom = new DOMDocument();
		/** @var DOMElement|false */
		$element = false;

		/** @var string,string|string[]|bool|int */
		$targetValue = '';
		/** @var array<string,string|string[]|bool|int> */
		// @phpstan-ignore-next-line
		if (isset($this->smarty->tpl_vars['values'])) {
			$values = $this->smarty->tpl_vars['values']->value;
			if (isset($values[$targetKey])) {
				$targetValue = $values[$targetKey];
			}
		}

		switch ($this->params['type']) {
			case 'textarea': {
					$element = $dom->createElement('textarea');

					$text = $dom->createTextNode($targetValue);
					$element->appendChild($text);
				}
				break;

			default: {
					$element = $dom->createElement('input');
					$element->setAttribute('type', $this->params['type']);
					$element->setAttribute('value', $targetValue);
				}
				break;
		}
		// @phpstan-ignore-next-line
		if (!$element) {
			throw new InvalidOperationException();
		}
		$dom->appendChild($element);

		$element->setAttribute('name', $targetKey);
		$ignoreKeys = ['key', 'type', 'value'];
		foreach ($this->params as $key => $value) {
			if (array_search($key, $ignoreKeys) !== false) {
				continue;
			}
			$booleanAttrs = ['readonly', 'disabled'];
			if (ArrayUtility::contains($booleanAttrs, $key)) {
				if (TypeConverter::parseBoolean($value)) {
					$element->setAttribute($key, '');
				}
				continue;
			}
			$element->setAttribute($key, $value);
		}
		if ($hasError) {
			$className = $element->getAttribute('class');
			if (StringUtility::isNullOrEmpty($className)) {
				$className = 'error';
			} else {
				$className .= ' error';
			}
			$element->setAttribute('class', $className);
		}

		if ($showAutoError) {
			return $dom->saveHTML() . $this->showErrorMessagesFunction->functionBody(['key' => $targetKey], $this->smarty);
		}
		return $dom->saveHTML(); // @phpstan-ignore-line
	}
}
