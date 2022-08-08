<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Environment;
use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\StringUtility;
use PeServer\Core\UrlUtility;

/**
 * 指定された属性値をHTMLの属性=値に変換する
 *
 *  * リビジョンをキャッシュバスターとして適用する
 *
 * $params
 *  * attr: 属性名
 *  * value: 属性値
 */
class SourceFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'source';
	}

	protected function functionBodyImpl(): string
	{
		/** @var string */
		$attributeName = ArrayUtility::getOr($this->params, 'attr', InitialValue::EMPTY_STRING);
		if (StringUtility::isNullOrEmpty($attributeName)) {
			return InitialValue::EMPTY_STRING;
		}

		$valuePath = ArrayUtility::getOr($this->params, 'value', InitialValue::EMPTY_STRING);
		if (StringUtility::isNullOrEmpty($valuePath)) {
			return InitialValue::EMPTY_STRING;
		}

		$ignoreAsset = UrlUtility::isIgnoreCaching($valuePath);

		$resourcePath = $valuePath;
		if (!$ignoreAsset) {
			$resourcePath .= '?' . Environment::getRevision();
		}

		return $attributeName . '="' . $resourcePath . '"';
	}
}
