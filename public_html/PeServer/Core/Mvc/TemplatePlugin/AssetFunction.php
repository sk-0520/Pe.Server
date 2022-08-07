<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \DOMElement;
use PeServer\Core\Environment;
use PeServer\Core\IOUtility;
use PeServer\Core\PathUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\InitialValue;
use PeServer\Core\StringUtility;
use PeServer\Core\TypeUtility;
use PeServer\Core\Html\HtmlDocument;
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

	public function getFunctionName(): string
	{
		return 'asset';
	}

	private function isIgnoreAsset(string $sourcePath): bool
	{
		$ignoreAsset =
			StringUtility::startsWith($sourcePath, '//', false)
			||
			StringUtility::startsWith($sourcePath, 'https://', false)
			||
			StringUtility::startsWith($sourcePath, 'http://', false)
			||
			StringUtility::contains($sourcePath, '?', false);

		return $ignoreAsset;
	}

	/**
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	protected function functionBodyImpl(): string
	{
		/** @var string */
		$sourcePath = ArrayUtility::getOr($this->params, 'file', InitialValue::EMPTY_STRING);
		if (StringUtility::isNullOrEmpty($sourcePath)) {
			return InitialValue::EMPTY_STRING;
		}

		$isProduction = Environment::isProduction();

		$fileExtension = PathUtility::getFileExtension($sourcePath);
		$extension = StringUtility::toLower($fileExtension);

		$ignoreAsset = $this->isIgnoreAsset($sourcePath);

		$resourcePath = $sourcePath;
		if (!$ignoreAsset) {
			if ($isProduction) {
				$dir = PathUtility::getDirectoryPath($sourcePath);
				$file = PathUtility::getFileNameWithoutExtension($sourcePath);

				$resourcePath = $dir . '/' . $file . '.min.' . $fileExtension;
			}

			$resourcePath .= '?' . Environment::getRevision();
		}

		$dom = new HtmlDocument();
		if (!$isProduction) {
			$dom->addComment(StringUtility::dump($this->params));
		}

		$autoSize = TypeUtility::parseBoolean(ArrayUtility::getOr($this->params, 'auto_size', 'false'));
		$include = TypeUtility::parseBoolean(ArrayUtility::getOr($this->params, 'include', 'false'));

		$filePath = PathUtility::combine($this->argument->rootDirectoryPath, $sourcePath);
		if (($autoSize || $include) || !IOUtility::existsFile($filePath)) {
			// @phpstan-ignore-next-line nullは全取得だからOK
			foreach ($this->argument->engine->getTemplateDir(null) as $dir) {
				$path = PathUtility::combine($dir, $sourcePath);
				if (IOUtility::existsFile($path)) {
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

					$content = IOUtility::readContent($filePath);
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
					$content = IOUtility::readContent($filePath);
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
			if (ArrayUtility::containsValue($skipAttributes, $key)) {
				continue;
			}
			$element->setAttribute($key, $value);
		}

		return $dom->build();
	}
}
