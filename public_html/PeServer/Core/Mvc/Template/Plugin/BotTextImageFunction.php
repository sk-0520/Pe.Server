<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use \Throwable;
use PeServer\Core\Image\Alignment;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Binary;
use PeServer\Core\CoreUtility;
use PeServer\Core\Cryptography;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\ImageSetting;
use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Point;
use PeServer\Core\Image\Rectangle;
use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Image\Size;
use PeServer\Core\Image\TextSetting;
use PeServer\Core\DefaultValue;
use PeServer\Core\Mvc\Template\Plugin\TemplateFunctionBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\OutputBuffer;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
use PeServer\Core\Throws\TemplateException;
use PeServer\Core\Throws\Throws;
use PeServer\Core\TypeUtility;

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
 *  * font-size: ホントサイズ(未指定は 12.0)。
 *  * background-color: 背景色
 *  * foreground-color: 前景色
 *  * obfuscate-level: 難読化(未指定は 0 )。 未実装。
 */
class BotTextImageFunction extends TemplateFunctionBase
{
	#region define

	private const HASH_ALGORITHM = 'sha256';

	#endregion

	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	#region TemplateFunctionBase

	public function getFunctionName(): string
	{
		return 'bot_text_image';
	}

	private function functionBodyCore(): string
	{
		/** @var string */
		$text = Arr::getOr($this->params, 'text', Text::EMPTY);
		/** @var string */
		$alt = Arr::getOr($this->params, 'alt', Text::EMPTY);
		/**
		 * @var int
		 * @phpstan-var positive-int
		 */
		$width = (int)Arr::getOr($this->params, 'width', 100);
		/**
		 * @var int
		 * @phpstan-var positive-int
		 */
		$height = (int)Arr::getOr($this->params, 'height', 100);
		/** @var float */
		$fontSize = (float)Arr::getOr($this->params, 'font-size', '12.5');
		/** @var string */
		$className = Arr::getOr($this->params, 'class', Text::EMPTY);
		/** @var string */
		$backgroundColorText = Arr::getOr($this->params, 'background-color', '#eeeeee');
		$backgroundColor = RgbColor::fromHtmlColorCode($backgroundColorText);
		/** @var string */
		$foregroundColorText = Arr::getOr($this->params, 'foreground-color', '#0f0f0f');
		$foregroundColor = RgbColor::fromHtmlColorCode($foregroundColorText);
		$obfuscateLevel = TypeUtility::parseBoolean(Arr::getOr($this->params, 'obfuscate-level', 0));

		$size = new Size($width, $height);
		$fontFilePath = CoreUtility::DEFAULT_FONT_FILE_PATH;
		$textSetting = new TextSetting(Alignment::HORIZONTAL_CENTER, Alignment::VERTICAL_CENTER, $fontFilePath, 0);

		$image = Graphics::create($size);
		$image->setDpi(new Size(300, 300));

		$rectangle = new Rectangle(new Point(0, 0), $size);
		$image->fillRectangle($backgroundColor, $rectangle);
		$image->drawText($text, $fontSize, $rectangle, $foregroundColor, $textSetting);

		$htmlSource = $image->exportHtmlSource(ImageSetting::png());
		$image->dispose();

		$dom = new HtmlDocument();
		$img = $dom->addElement('img');
		$img->setAttribute('src', $htmlSource);

		$textHash = Cryptography::generateHashString(self::HASH_ALGORITHM, new Binary($text));
		$img->setAttribute('data-hash', $textHash);
		if (!Text::isNullOrWhiteSpace($className)) {
			$img->setAttribute('class', $className);
		}

		if (!Text::isNullOrWhiteSpace($alt)) {
			$img->setAttribute('alt', $alt);
		}

		return $dom->build();
	}

	protected function functionBodyImpl(): string
	{
		try {
			return $this->functionBodyCore();
		} catch (Throwable $ex) {
			Throws::reThrow(TemplateException::class, $ex);
		}
	}

	#endregion
}
