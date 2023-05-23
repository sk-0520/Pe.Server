<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Html\HtmlElement;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Mvc\Template\Plugin\TemplateFunctionBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Mvc\Template\Plugin\ShowErrorMessagesFunction;

/**
 * 入力要素のヘルパー。
 *
 * $params
 *  * key: 対象キー, valuesと紐づく
 *  * type: 対象のinput[type="*"]かtextareaを指定。不明時は input としてそのまま生成される。radio/checkboxは想定していないのでなんか別の方法を考えた方がいい
 *  * auto_error: 真の場合にエラー表示も自動で行う(show_error_messages関数の内部呼び出し)(未指定は true)。
 */
class InputHelperFunction extends TemplateFunctionBase
{
	private ShowErrorMessagesFunction $showErrorMessagesFunction;

	public function __construct(TemplatePluginArgument $argument, ShowErrorMessagesFunction $showErrorMessagesFunction)
	{
		parent::__construct($argument);

		$this->showErrorMessagesFunction = $showErrorMessagesFunction;
	}

	#region TemplateFunctionBase

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
		/** @var string */
		$type = Arr::getOr($this->params, 'type', Text::EMPTY);

		switch ($type) {
			case 'textarea': {
					$element = $dom->addElement('textarea');
					if(is_array($targetValue)) {
						$element->addText(Text::join(PHP_EOL, $targetValue));
					} else {
						$element->addText(strval($targetValue));
					}
					return $element;
				}

			default: {
					$element = $dom->addElement('input');
					if (!Text::isNullOrWhiteSpace($type)) {
						$element->setAttribute('type', $type);
					}
					$element->setAttribute('value', strval($targetValue)); //@phpstan-ignore-line
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

		if (Arr::containsValue($booleanAttrs, $name)) {
			$b = TypeUtility::parseBoolean($value);
			$element->setAttribute($name, $b);
		} else {
			$element->setAttribute($name, $value);
		}
	}

	protected function functionBodyImpl(): string
	{
		$targetKey = $this->params['key']; // 必須

		$showAutoError = TypeUtility::parseBoolean(Arr::getOr($this->params, 'file', true));

		$hasError = false;
		if ($this->existsError()) {
			$errors = $this->getErrors();
			if (Arr::tryGet($errors, $targetKey, $result)) {
				$hasError = 0 < Arr::getCount($result);
			}
		}

		/** @var string|string[]|bool|int */
		$targetValue = Text::EMPTY;
		if ($this->existsValues()) {
			$values = $this->getValues();
			if (Arr::tryGet($values, $targetKey, $result)) {
				$targetValue = $result;
			}
		}

		$dom = new HtmlDocument();
		$element = $this->addMainElement($dom, $targetValue);

		$element->setAttribute('id', $targetKey);
		$element->setAttribute('name', $targetKey);

		$ignoreKeys = ['key', 'type', 'value']; // idは渡されたものを優先
		foreach ($this->params as $key => $value) {
			if (Arr::containsValue($ignoreKeys, $key)) {
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

	#endregion
}
