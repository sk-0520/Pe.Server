<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\ArrayUtility;
use PeServer\Core\HtmlDocument;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\CoreError;

/**
 * テキストから画像生成
 *
 * $params
 *   text: 画像化するテキスト。
 */
class TextImageFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'text_image';
	}

	protected function functionBodyImpl(): string
	{
		$text = ArrayUtility::getOr($this->params, 'text', '');
		$alt = ArrayUtility::getOr($this->params, 'alt', '');

		$image = \imagecreate(100, 100);
		if ($image === false) {
			throw new CoreError();
		}

		$dom = new HtmlDocument();
		$img = $dom->addElement('img');

		$img->setAttribute('data-hash', hash('sha256', $text));

		if (!StringUtility::isNullOrWhiteSpace($alt)) {
			$img->setAttribute('alt', $alt);
		} else {
			$img->setAttribute('alt', '💩');
		}

		return $dom->build();
	}
}
