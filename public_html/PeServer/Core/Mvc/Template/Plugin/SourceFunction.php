<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\ArrayUtility;
use PeServer\Core\DefaultValue;
use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Plugin\TemplateFunctionBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Text;
use PeServer\Core\Web\UrlUtility;

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
		$attributeName = ArrayUtility::getOr($this->params, 'attr', DefaultValue::EMPTY_STRING);
		if (Text::isNullOrEmpty($attributeName)) {
			return DefaultValue::EMPTY_STRING;
		}

		$valuePath = ArrayUtility::getOr($this->params, 'value', DefaultValue::EMPTY_STRING);
		if (Text::isNullOrEmpty($valuePath)) {
			return DefaultValue::EMPTY_STRING;
		}

		$ignoreAsset = UrlUtility::isIgnoreCaching($valuePath);

		$resourcePath = $valuePath;
		if (!$ignoreAsset) {
			$resourcePath .= '?' . Environment::getRevision();
		}

		return $attributeName . '="' . $resourcePath . '"';
	}
}
