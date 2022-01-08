<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\ArrayUtility;
use PeServer\Core\HtmlDocument;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\OutputBuffer;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\CoreError;

/**
 * Bot用にテキストから画像生成。
 *
 * 一旦ただの文字列画像化で、botに対して何の効力もないけどインターフェイス的なところでここに集約させたいのでこのままリリース。
 * まぁ最低限の処理くらいは捌けるでしょ。
 *
 * $params
 *  * text: 画像化するテキスト。
 *  * alt: 代替文言(いらんかもね)。
 *  * width: 指定時の横幅(現状 未指定は 100)。
 *  * height: 指定時の高さ(現状 未指定は 100)。
 */
class BotTextImageFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'bot_text_image';
	}

	protected function functionBodyImpl(): string
	{
		$text = ArrayUtility::getOr($this->params, 'text', '');
		$alt = ArrayUtility::getOr($this->params, 'alt', '');
		$width = (int)ArrayUtility::getOr($this->params, 'width', 100);
		$height = (int)ArrayUtility::getOr($this->params, 'height', 100);

		$image = imagecreate($width, $height);
		if ($image === false) {
			throw new CoreError();
		}
		$background_color = imagecolorallocate($image, 0xee, 0xee, 0xee);
		if ($background_color === false) {
			throw new CoreError();
		}
		$text_color = imagecolorallocate($image, 0x0f, 0x0f, 0x0f);
		if ($text_color === false) {
			throw new CoreError();
		}
		imagestring($image, 5, 0, 0, $text, $text_color);

		$binary = OutputBuffer::get(function () use ($image) {
			imagepng($image);
		});


		$dom = new HtmlDocument();
		$img = $dom->addElement('img');
		$img->setAttribute('src', 'data:image/png;base64,' . $binary->toBase64());

		$img->setAttribute('data-hash', hash('sha256', $text));

		if (!StringUtility::isNullOrWhiteSpace($alt)) {
			$img->setAttribute('alt', $alt);
		}

		return $dom->build();
	}
}
