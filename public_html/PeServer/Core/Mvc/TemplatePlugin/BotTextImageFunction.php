<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use PeServer\Core\ArrayUtility;
use PeServer\Core\FileUtility;
use PeServer\Core\HtmlDocument;
use PeServer\Core\OutputBuffer;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\TemplateException;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\TypeConverter;

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
 *  * font-size: ホントサイズ(未指定は 12.0 )。
 *  * background-color: 背景色
 *  * foreground-color: 前景色
 *  * obfuscate-level: 難読化(未指定は 0 )。 未実装。
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

	/**
	 * Undocumented function
	 *
	 * @param string $htmlColor
	 * @return array{r:int,g:int,b:int}
	 */
	private function toColor(string $htmlColor): array
	{
		return [
			'r' => (int)hexdec(substr($htmlColor, 1, 2)),
			'g' => (int)hexdec(substr($htmlColor, 3, 2)),
			'b' => (int)hexdec(substr($htmlColor, 5, 2)),
		];
	}

	protected function functionBodyImpl(): string
	{
		/** @var string */
		$text = ArrayUtility::getOr($this->params, 'text', '');
		/** @var string */
		$alt = ArrayUtility::getOr($this->params, 'alt', '');
		/** @var int */
		$width = (int)ArrayUtility::getOr($this->params, 'width', 100);
		/** @var int */
		$height = (int)ArrayUtility::getOr($this->params, 'height', 100);
		/** @var float */
		$fontSize = (float)ArrayUtility::getOr($this->params, 'font-size', '12.5');
		/** @var string */
		$className = ArrayUtility::getOr($this->params, 'class', '');
		/** @var string */
		$backgroundColorText = ArrayUtility::getOr($this->params, 'background-color', '#eeeeee');
		$backgroundColors = $this->toColor($backgroundColorText);
		/** @var string */
		$foregroundColorText = ArrayUtility::getOr($this->params, 'foreground-color', '#0f0f0f');
		$foregroundColors = $this->toColor($foregroundColorText);
		$obfuscateLevel = TypeConverter::parseBoolean(ArrayUtility::getOr($this->params, 'obfuscate-level', 0));

		$rectX = 0;
		$rectY = 0;
		$rectWidth = $width - 1;
		$rectHeight = $height - 1;

		$fontFileName = 'migmix-1m-regular.ttf';
		$fontFilePath = FileUtility::joinPath($this->argument->baseDirectoryPath, 'Core', 'Libs', 'fonts', 'migmix', $fontFileName);

		$box = imageftbbox($fontSize, 0, $fontFilePath, $text);
		if ($box === false) {
			throw new TemplateException();
		}

		$textWidth = $box[4] - $box[0];
		$textHeight = $box[1] - $box[5];

		$x = (int)($rectX + (($rectWidth - $rectX - $textWidth) / 2));
		$y = (int)($rectY + (($rectHeight - $rectY - $textHeight) / 2) - $box[5]);

		$image = imagecreatetruecolor($width, $height);
		if ($image === false) {
			throw new TemplateException();
		}
		imageresolution($image, 300, 300);

		$backgroundColor = imagecolorallocate($image, $backgroundColors['r'], $backgroundColors['g'], $backgroundColors['b']);
		if ($backgroundColor === false) {
			throw new TemplateException();
		}
		$textColor = imagecolorallocate($image, $foregroundColors['r'], $foregroundColors['g'], $foregroundColors['b']);
		if ($textColor === false) {
			throw new TemplateException();
		}

		imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColor);
		imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontFilePath, $text);

		$binary = OutputBuffer::get(function () use ($image) {
			imagepng($image);
		});


		$dom = new HtmlDocument();
		$img = $dom->addElement('img');
		$img->setAttribute('src', 'data:image/png;base64,' . $binary->toBase64());

		$textHash = hash('sha256', $text);
		if ($textHash === false) { // @phpstan-ignore-line
			throw new TemplateException();
		}
		$img->setAttribute('data-hash', $textHash);
		if (!StringUtility::isNullOrWhiteSpace($className)) {
			$img->setAttribute('class', $className);
		}

		if (!StringUtility::isNullOrWhiteSpace($alt)) {
			$img->setAttribute('alt', $alt);
		}

		return $dom->build();
	}
}
