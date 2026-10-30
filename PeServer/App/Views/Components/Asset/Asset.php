<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Asset;

use Override;
use PeServer\Core\DI\Inject;
use PeServer\Core\Environment;
use PeServer\Core\Html\HtmlCommentElement;
use PeServer\Core\Image\ImageInformation;
use PeServer\Core\IO\File;
use PeServer\Core\Mvc\Template\Node\Html\JavaScriptSourceNode;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\Template\Node\Comment;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLImgAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLLinkAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\Html\CssSourceNode;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentBase;
use PeServer\Core\Mvc\Template\Node\Html\HtmlComponentTrait;
use PeServer\Core\Mvc\Template\Node\INode;
use PeServer\Core\Mvc\Template\Node\NoneNode;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;

/**
 * @extends HtmlComponentBase<\PeServer\App\Views\Components\Asset\AssetProps>
 */
class Asset extends HtmlComponentBase
{
	/** @template-use HtmlComponentTrait<\PeServer\App\Views\Components\Asset\AssetProps> */
	use HtmlComponentTrait;

	#region function

	/**
	 * キャッシュ考慮不要な(HTTP)パスか。
	 *
	 * @param string $path
	 * @return bool
	 */
	private function isIgnoreCaching(string $path): bool
	{
		$isExternal =
			Text::startsWith($path, '//', false)
			||
			Text::startsWith($path, 'https://', false)
			||
			Text::startsWith($path, 'http://', false)
			||
			Text::contains($path, '?', false);

		return $isExternal;
	}

	#endregion

	#region HtmlComponentBase

	#[Override]
	protected function getNode(): INode
	{
		$sourcePath = Text::trim($this->props->file);

		if (Text::isNullOrWhiteSpace($sourcePath)) {
			return NoneNode::get();
		}

		$isProduction = $this->context->environment->isProduction();

		$fileExtension = Path::getFileExtension($sourcePath);
		$extension = Text::toLower($fileExtension);

		$ignoreAsset = $this->isIgnoreCaching($sourcePath);

		$resourcePath = $sourcePath;
		if (!$ignoreAsset) {
			if ($isProduction) {
				$minimalPath = Text::trim(Path::setEnvironmentName($sourcePath, 'min'));
				$physicalPath = Path::combine($this->context->program->publicDirectory, $minimalPath);
				if (IOUtility::exists($physicalPath)) {
					$resourcePath = Text::replace($minimalPath, "\\", '/');
				}
			}

			$resourcePath .= '?' . $this->context->environment->getRevision();
		}

		/** @var INode[] $nodes */
		$nodes = [];

		if (!$isProduction) {
			$nodes[] = new Comment(Text::dump($this->props));
		}

		if ($this->props->rel !== null) {
			switch ($this->props->rel) {
				case Rel::Icon:
					$nodes[] = $this->context->builder->link(
						new HTMLLinkAttributes(
							rel: Rel::Icon,
							href: $resourcePath,
							type: match ($extension) {
								'png' => 'image/png',
								'jpg', 'jpeg' => 'image/jpeg',
								'gif' => 'image/gif',
								'ico' => 'image/x-icon',
								'svg' => 'image/svg+xml',
								default => 'application/octet-stream',
							}
						)
					);
					break;

				default:
					throw new NotImplementedException('Unsupported rel type: ' . Text::toString($this->props->rel->value));
			}
		} else {
			$baseDirectory = $this->props->includeIsPublic === true
				? $this->context->program->publicDirectory
				: $this->context->program->rootDirectory;

			$filePath = Path::combine($baseDirectory, $sourcePath);

			switch ($extension) {
				case 'css':
					if ($this->props->include) {
						$content = File::readContent($filePath);

						$nodes[] = $this->context->builder->style(
							new HTMLStyleAttributes(),
							new CssSourceNode($content->toString())
						);
					} else {
						$nodes[] = $this->context->builder->link(
							new HTMLLinkAttributes(
								rel: Rel::Stylesheet,
								href: $resourcePath,
								type: 'text/css'
							)
						);
					}
					break;

				case 'js':
					if ($this->props->include) {
						$content = File::readContent($filePath);

						$nodes[] = $this->context->builder->script(
							new HTMLScriptAttributes(),
							new JavaScriptSourceNode($content->toString())
						);
					} else {
						$nodes[] = $this->context->builder->script(
							new HTMLScriptAttributes(
								src: $resourcePath,
							)
						);
					}
					break;

				case 'png':
				case 'jpeg':
				case 'jpg':
					if (!$ignoreAsset && $this->props->include) {
						$info = [
							'width' => null,
							'height' => null,
						];

						$imageInfo = ImageInformation::load($filePath);

						if ($this->props->autoSize) {
							$info['width'] = $imageInfo->size->width;
							$info['height'] = $imageInfo->size->height;
						}

						$content = File::readContent($filePath);
						$base64 = $content->toBase64();
						$inline = 'data:' . $imageInfo->mime . ';base64,' . $base64;

						$nodes[] = $this->context->builder->img(
							new HTMLImgAttributes(
								src: $inline,
								alt: $this->props->alt,
								width: $info['width'],
								height: $info['height'],
							)
						);
					} else {
						$nodes[] = $this->context->builder->img(
							new HTMLImgAttributes(
								src: $resourcePath,
								alt: $this->props->alt,
								width: $this->props->width,
								height: $this->props->height,
							)
						);
					}
					break;

				default:
					throw new NotImplementedException('Unsupported file extension: ' . $extension);
			}
		}

		return new Fragment($nodes);
	}

	#endregion
}
