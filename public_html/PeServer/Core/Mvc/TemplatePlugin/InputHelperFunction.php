<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\ArrayUtility;
use PeServer\Core\HtmlDocument;
use PeServer\Core\HtmlElement;
use PeServer\Core\StringUtility;
use PeServer\Core\TypeConverter;
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

	/**
	 * Undocumented function
	 *
	 * @param HtmlDocument $dom
	 * @param string|string[]|bool|int $targetValue
	 * @return HtmlElement
	 */
	private function addMainElement(HtmlDocument $dom, mixed $targetValue): HtmlElement
	{
		$type = ArrayUtility::getOr($this->params, 'type', '');

		switch ($type) {
			case 'textarea': {
					$element = $dom->addElement('textarea');
					$element->addText(strval($targetValue));
					return $element;
				}

			default: {
					$element = $dom->addElement('input');
					if (!StringUtility::isNullOrWhiteSpace($type)) {
						$element->setAttribute('type', $type);
					}
					$element->setAttribute('value', strval($targetValue));
					return $element;
				}
		}
	}

	private function setElementAttribute(HtmlElement $element, string $name, string $value): void
	{
		$booleanAttrs = [
			'readonly',
			'disabled',
			'checked',
			'selected',
			'autofocus',
			'required'
		];

		if (ArrayUtility::contains($booleanAttrs, $name)) {
			$b = TypeConverter::parseBoolean($value);
			$element->setAttribute($name, $b);
		} else {
			$element->setAttribute($name, $value);
		}
	}

	protected function functionBodyImpl(): string
	{
		$targetKey = $this->params['key']; // 必須

		$showAutoError = TypeConverter::parseBoolean(ArrayUtility::getOr($this->params, 'file', true));

		$hasError = false;
		if ($this->existsError()) {
			$errors = $this->getErrors();
			if (ArrayUtility::tryGet($errors, $targetKey, $result)) {
				$hasError = 0 < ArrayUtility::getCount($result);
			}
		}

		/** @var string|string[]|bool|int */
		$targetValue = '';
		if ($this->existsValues()) {
			$values = $this->getValues();
			if (ArrayUtility::tryGet($values, $targetKey, $result)) {
				$targetValue = $result;
			}
		}

		$dom = new HtmlDocument();
		$element = $this->addMainElement($dom, $targetValue);

		$element->setAttribute('id', $targetKey);
		$element->setAttribute('name', $targetKey);

		$ignoreKeys = ['key', 'type', 'value']; // idは渡されたものを優先
		foreach ($this->params as $key => $value) {
			if (ArrayUtility::contains($ignoreKeys, $key)) {
				continue;
			}
			$this->setElementAttribute($element, $key, $value);
		}

		if ($hasError) {
			$element->addClass('error');
		}

		if ($showAutoError) {
			return $dom->build() . $this->showErrorMessagesFunction->functionBody(['key' => $targetKey], $this->template);
		}
		return $dom->build();
	}
}
