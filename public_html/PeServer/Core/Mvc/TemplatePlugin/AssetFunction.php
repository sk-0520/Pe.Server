<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \DOMElement;
use PeServer\Core\Environment;
use PeServer\Core\FileUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\HtmlDocument;
use PeServer\Core\StringUtility;
use PeServer\Core\TypeConverter;
use PeServer\Core\Throws\TemplateException;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;

/**
 * 指定されたリソースをHTMLとして読み込む。
 *
 *  * 本番環境であればミニファイされたリソースを読もうとする
 *  * リビジョンをキャッシュバスターとして適用する
 *
 * $params
 *  * file: 対象リソース
 *  * auto_size: true/false trueの場合に実イメージサイズを読み込む
 *  * include: true/false trueの場合にファイルの中身を使用する(結構適当)
 *  * その他: 全部設定される
 */
class AssetFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'asset';
	}

	protected function functionBodyImpl(): string
	{
		$sourcePath = ArrayUtility::getOr($this->params, 'file', '');
		if (StringUtility::isNullOrEmpty($sourcePath)) {
			return '';
		}

		$isProduction = Environment::isProduction();

		$fileExtension = FileUtility::getFileExtension($sourcePath);
		$extension = StringUtility::toLower($fileExtension);

		$ignoreAsset =
			StringUtility::startsWith($sourcePath, '//', false)
			||
			StringUtility::startsWith($sourcePath, 'https://', false)
			||
			StringUtility::startsWith($sourcePath, 'http://', false)
			||
			StringUtility::contains($sourcePath, '?', false);

		$resourcePath = $sourcePath;
		if (!$ignoreAsset) {
			if ($isProduction) {
				$dir = FileUtility::getDirectoryPath($sourcePath);
				$file = FileUtility::getFileNameWithoutExtension($sourcePath);

				$resourcePath = $dir . '/' . $file . '.min.' . $fileExtension;
			}

			$resourcePath .= '?' . Environment::getRevision();
		}

		$dom = new HtmlDocument();
		if (!$isProduction) {
			$dom->addComment(StringUtility::dump($this->params));
		}

		$autoSize = TypeConverter::parseBoolean(ArrayUtility::getOr($this->params, 'auto_size', false));
		$include = TypeConverter::parseBoolean(ArrayUtility::getOr($this->params, 'include', false));

		$filePath = FileUtility::joinPath($this->argument->rootDirectoryPath, $sourcePath);
		if (($autoSize || $include) || !is_file($filePath)) {
			// @phpstan-ignore-next-line nullは全取得だからOK
			foreach ($this->argument->engine->getTemplateDir(null) as $dir) {
				$path = FileUtility::joinPath($dir, $sourcePath);
				if (is_file($path)) {
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
		/** @var DOMElement */
		$element = null;

		switch ($extension) {
			case 'css':
				if ($include) {
					$element = $dom->addElement('style');

					$content = FileUtility::readContent($filePath);
					$element->addText($content->toString());
				} else {
					$element = $dom->addElement('link');

					$element->setAttribute('rel', 'stylesheet');
					$element->setAttribute('href', $resourcePath);
					$skipAttributes = array_merge($skipAttributes, ['rel', 'href']);
				}
				break;

			case 'js':
				$element = $dom->addElement('script');

				if ($include) {
					$content = FileUtility::readContent($filePath);
					$element->addText($content->toString());
				} else {
					$element->setAttribute('src', $resourcePath);
					$skipAttributes = array_merge($skipAttributes, ['src']);
				}
				break;

			case 'png':
			case 'jpeg':
			case 'jpg':
				$element = $dom->addElement('img');

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

		foreach ($this->params as $key => $value) {
			if (ArrayUtility::contains($skipAttributes, $key)) {
				continue;
			}
			$element->setAttribute($key, $value);
		}

		return $dom->build();
	}
}
