<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\Alignment;
use \Throwable;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Binary;
use PeServer\Core\CoreUtility;
use PeServer\Core\Cryptography;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\ImageOption;
use PeServer\Core\Image\ImageType;
use PeServer\Core\Image\Point;
use PeServer\Core\Image\Rectangle;
use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Image\Size;
use PeServer\Core\Image\TextSetting;
use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\OutputBuffer;
use PeServer\Core\PathUtility;
use PeServer\Core\StringUtility;
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
	private const HASH_ALGORITHM = 'sha256';

	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'bot_text_image';
	}

	private function functionBodyCore(): string
	{
		/** @var string */
		$text = ArrayUtility::getOr($this->params, 'text', InitialValue::EMPTY_STRING);
		/** @var string */
		$alt = ArrayUtility::getOr($this->params, 'alt', InitialValue::EMPTY_STRING);
		/**
		 * @var int
		 * @phpstan-var positive-int
		 */
		$width = (int)ArrayUtility::getOr($this->params, 'width', 100);
		/**
		 * @var int
		 * @phpstan-var positive-int
		 */
		$height = (int)ArrayUtility::getOr($this->params, 'height', 100);
		/** @var float */
		$fontSize = (float)ArrayUtility::getOr($this->params, 'font-size', '12.5');
		/** @var string */
		$className = ArrayUtility::getOr($this->params, 'class', InitialValue::EMPTY_STRING);
		/** @var string */
		$backgroundColorText = ArrayUtility::getOr($this->params, 'background-color', '#eeeeee');
		$backgroundColor = RgbColor::fromHtmlColorCode($backgroundColorText);
		/** @var string */
		$foregroundColorText = ArrayUtility::getOr($this->params, 'foreground-color', '#0f0f0f');
		$foregroundColor = RgbColor::fromHtmlColorCode($foregroundColorText);
		$obfuscateLevel = TypeUtility::parseBoolean(ArrayUtility::getOr($this->params, 'obfuscate-level', 0));

		$size = new Size($width, $height);
		$fontFilePath = PathUtility::combine($this->argument->baseDirectoryPath, ...CoreUtility::getDefaultFontParts());
		$textSetting = new TextSetting(Alignment::HORIZONTAL_CENTER, Alignment::VERTICAL_CENTER, $fontFilePath, 0);

		$image = Graphics::create($size);
		$image->setDpi(new Size(300, 300));

		$rectangle = new Rectangle(new Point(0, 0), $size);
		$image->fillRectangle($backgroundColor, $rectangle);
		$image->drawText($text, $fontSize, $rectangle, $foregroundColor, $textSetting);

		$htmlSource = $image->exportHtmlSource(ImageOption::png());
		$image->dispose();

		$dom = new HtmlDocument();
		$img = $dom->addElement('img');
		$img->setAttribute('src', $htmlSource);

		$textHash = Cryptography::generateHashString(self::HASH_ALGORITHM, new Binary($text));
		$img->setAttribute('data-hash', $textHash);
		if (!StringUtility::isNullOrWhiteSpace($className)) {
			$img->setAttribute('class', $className);
		}

		if (!StringUtility::isNullOrWhiteSpace($alt)) {
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
}
