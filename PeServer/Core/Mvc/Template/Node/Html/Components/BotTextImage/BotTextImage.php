<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Components\BotTextImage;

use Override;
use PeServer\Core\Binary;
use PeServer\Core\CoreDefines;
use PeServer\Core\Cryptography;
use PeServer\Core\Image\Color\RgbColor;
use PeServer\Core\Image\Graphics;
use PeServer\Core\Image\HorizontalAlignment;
use PeServer\Core\Image\ImageSetting;
use PeServer\Core\Image\Point;
use PeServer\Core\Image\Rectangle;
use PeServer\Core\Image\Size;
use PeServer\Core\Image\TextSetting;
use PeServer\Core\Image\VerticalAlignment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLImgAttributes;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Text;
use PeServer\Core\Throws\TemplateException;

/**
 * @extends HtmlComponentBase<BotTextImageProps>
 */
class BotTextImage extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<BotTextImageProps> */
	use HtmlComponentTrait;

	#[Override]
	protected function getNode(): INode
	{
		$size = new Size($this->props->width, $this->props->height);
		$backgroundColor = RgbColor::fromHtmlColorCode($this->props->backgroundColor);
		$foregroundColor = RgbColor::fromHtmlColorCode($this->props->foregroundColor);
		$textSetting = new TextSetting(
			HorizontalAlignment::Center,
			VerticalAlignment::Center,
			CoreDefines::DEFAULT_FONT_FILE_PATH,
			0
		);
		$image = Graphics::create($size);
		try {
			$rectangle = new Rectangle(new Point(0, 0), $size);
			$image->fillRectangle($backgroundColor, $rectangle);
			$image->drawText($this->props->text, $this->props->fontSize, $rectangle, $foregroundColor, $textSetting);
			$source = $image->saveHtmlSource(ImageSetting::png());
		} finally {
			$image->dispose();
		}
		if (Text::isNullOrEmpty($source)) {
			throw new TemplateException('Bot text image source is empty.');
		}

		return $this->context->builder->img(new HTMLImgAttributes(
			src: $source,
			width: $this->props->width,
			height: $this->props->height,
			alt: Text::isNullOrWhiteSpace($this->props->alt) ? null : $this->props->alt,
			className: Text::isNullOrWhiteSpace($this->props->className) ? null : $this->props->className,
			data: new DataAttributes([
				'hash' => Cryptography::generateHashString('sha256', new Binary($this->props->text)),
			]),
		));
	}
}
