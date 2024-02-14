<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Plugin;

use PeServer\Core\Environment;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\File;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlTagElement;
use PeServer\Core\Throws\TemplateException;
use PeServer\Core\Mvc\Template\Plugin\TemplateFunctionBase;
use PeServer\Core\Mvc\Template\Plugin\TemplatePluginArgument;
use PeServer\Core\Web\UrlUtility;

/**
 * 指定されたリソースをHTMLとして読み込む。
 *
 *  * 本番環境であればミニファイされたリソースを読もうとする
 *  * リビジョンをキャッシュバスターとして適用する
 *
 * $params
 *  * file: 対象リソース
 *  * auto_size: true/false trueの場合に実イメージサイズを読み込む(未指定は false)。
 *  * include: true/false trueの場合にファイルの中身を使用する(結構適当)(未指定は false)。
 *  * その他: 全部設定される
 */
class AssetFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

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

	#region TemplateFunctionBase

	public function getFunctionName(): string
	{
		return 'asset';
	}

	/**
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	protected function functionBodyImpl(): string
	{
		/** @var string */
		$sourcePath = Arr::getOr($this->params, 'file', Text::EMPTY);
		if (Text::isNullOrEmpty($sourcePath)) {
			return Text::EMPTY;
		}

		$isProduction = $this->argument->environment->isProduction();

		$fileExtension = Path::getFileExtension($sourcePath);
		$extension = Text::toLower($fileExtension);

		$ignoreAsset = $this->isIgnoreCaching($sourcePath);

		$resourcePath = $sourcePath;
		if (!$ignoreAsset) {
			if ($isProduction) {
				$minimalPath = Text::trim(Path::setEnvironmentName($sourcePath, 'min'));
				$physicalPath = Path::combine($this->argument->programContext->publicDirectory, $minimalPath);
				if (IOUtility::exists($physicalPath)) {
					$resourcePath = Text::replace($minimalPath, "\\", '/');
				}
			}

			$resourcePath .= '?' . $this->argument->environment->getRevision();
		}

		$dom = new HtmlDocument();
		if (!$isProduction) {
			$dom->addComment(Text::dump($this->params));
		}

		$autoSize = TypeUtility::parseBoolean(Arr::getOr($this->params, 'auto_size', 'false'));
		$include = TypeUtility::parseBoolean(Arr::getOr($this->params, 'include', 'false'));

		$filePath = Path::combine($this->argument->rootDirectoryPath, $sourcePath);
		if (($autoSize || $include) || !FIle::exists($filePath)) {
			// @phpstan-ignore-next-line nullは全取得だからOK
			foreach ($this->argument->engine->getTemplateDir(null) as $dir) {
				$path = Path::combine($dir, $sourcePath);
				if (IOUtility::exists($path)) {
					$filePath = $path;
					break;
				}
			}
		}

		$skipAttributes = [
			'file',
			'auto_size',
			'include',
		];
		/** @var HtmlTagElement|null */
		$element = null;

		if (Arr::tryGet($this->params, 'rel', $relValue)) {
			switch ($relValue) {
				case 'icon':
					$element = $dom->addTagElement('link');
					$element->setAttribute('href', $resourcePath);
					$skipAttributes = array_merge($skipAttributes, ['href']);
					break;
			}
		}

		if ($element === null) {
			switch ($extension) {
				case 'css':
					if ($include) {
						$element = $dom->addTagElement('style');

						$content = File::readContent($filePath);
						$element->addText($content->toString());
					} else {
						$element = $dom->addTagElement('link');

						$element->setAttribute('rel', 'stylesheet');
						$element->setAttribute('href', $resourcePath);
						$skipAttributes = array_merge($skipAttributes, ['rel', 'href']);
					}
					break;

				case 'js':
					$element = $dom->addTagElement('script');

					if ($include) {
						$content = File::readContent($filePath);
						$element->addText($content->toString());
					} else {
						$element->setAttribute('src', $resourcePath);
						$skipAttributes = array_merge($skipAttributes, ['src']);
					}
					break;

				case 'png':
				case 'jpeg':
				case 'jpg':
					$element = $dom->addTagElement('img');

					$element->setAttribute('src', $resourcePath);
					$skipAttributes = array_merge($skipAttributes, ['src']);

					if (!$ignoreAsset && ($autoSize || $include)) {
						$imageSize = getimagesize($filePath);
						if ($imageSize !== false) {
							$element->setAttribute('width', strval($imageSize[0]));
							$element->setAttribute('height', strval($imageSize[1]));
							$skipAttributes = array_merge($skipAttributes, ['width', 'height']);

							if ($include) {
								$content = file_get_contents($filePath);
								$base64 = base64_encode($content); // @phpstan-ignore-line しんどい
								$inline = 'data:' . $imageSize['mime'] . ';base64,' . $base64;
								$element->setAttribute('src', $inline);
							}
						}
					}
					break;

				default:
					throw new TemplateException($resourcePath);
			}
		}

		foreach ($this->params as $key => $value) {
			if (Arr::containsValue($skipAttributes, $key)) {
				continue;
			}
			$element->setAttribute($key, $value);
		}

		return $dom->build();
	}

	#endregion
}
