<?php

declare(strict_types=1);

namespace PeServer\Core\Html;

use DOMElement;
use Exception;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Html\HtmlElementBase;
use PeServer\Core\Html\HtmlXPath;
use PeServer\Core\Text;
use PeServer\Core\Throws\HtmlAttributeException;
use PeServer\Core\Throws\HtmlDocumentException;
use PeServer\Core\Throws\Throws;
use PeServer\Core\TypeUtility;
use Throwable;

/**
 * `DOMElement` ラッパー。
 */
final class HtmlTagElement extends HtmlElementBase
{
	#region variable

	/**
	 * 生で使用する用。
	 * @readonly
	 */
	public readonly DOMElement $raw;

	#endregion

	public function __construct(HtmlDocument $document, DOMElement $raw)
	{
		parent::__construct($document, $raw);
		$this->raw = $raw;
	}

	#endregion

	public function hasAttribute(string $qualifiedName): bool
	{
		return $this->raw->hasAttribute($qualifiedName);
	}

	public function isAttribute(string $qualifiedName): bool
	{
		if ($this->tryGetAttribute($qualifiedName, $value)) {
			return TypeUtility::parseBoolean($value);
		}

		return false;
	}

	public function getAttribute(string $qualifiedName): string
	{
		if(!$this->raw->hasAttribute($qualifiedName)) {
			throw new HtmlAttributeException($qualifiedName);
		}

		$attributeValue = $this->raw->getAttribute($qualifiedName);

		return $attributeValue;
	}

	/**
	 *
	 * @param string $qualifiedName
	 * @param string|null $result
	 * @return bool
	 * @phpstan-assert-if-true string $result
	 */
	public function tryGetAttribute(string $qualifiedName, string|null &$result): bool
	{
		$attributeValue = $this->raw->getAttribute($qualifiedName);
		if (Text::isNullOrEmpty($attributeValue)) {
			$result = null;
			return false;
		}

		$result = $attributeValue;

		return true;
	}

	/**
	 * 属性設定
	 *
	 * @param string $qualifiedName
	 * @param string|boolean $value 文字列の場合はそのまま設定。真偽値の場合、真であれば属性名の設定、偽であれば属性の削除
	 * @return void
	 */
	public function setAttribute(string $qualifiedName, string|bool $value): void
	{
		if (is_bool($value)) {
			if ($value) {
				$value = 'on';
			} else {
				if ($this->raw->hasAttribute($qualifiedName)) {
					$this->raw->removeAttribute($qualifiedName);
				}
				return;
			}
		}

		try {
			$result = $this->raw->setAttribute($qualifiedName, $value);
			if ($result === false) { // @phpstan-ignore-line
				throw new HtmlAttributeException();
			}
		} catch (Throwable $ex) {
			Throws::reThrow(HtmlAttributeException::class, $ex);
		}
	}

	/**
	 * クラス一覧を取得。
	 *
	 * @return string[]
	 */
	public function getClassList(): array
	{
		$classValue = $this->raw->getAttribute('class');
		if (Text::isNullOrWhiteSpace($classValue)) {
			return [];
		}

		return Text::split($classValue, ' ');
	}

	/**
	 * Undocumented function
	 *
	 * @param string[] $classNames
	 * @return void
	 */
	public function setClassList(array $classNames): void
	{
		$classValue = Text::join(' ', Arr::toUnique($classNames));
		$this->setAttribute('class', $classValue);
	}

	public function addClass(string $className): void
	{
		$list = $this->getClassList();
		if (!Arr::containsValue($list, $className)) {
			$list[] = $className;
			$this->setClassList($list);
		}
	}

	public function removeClass(string $className): void
	{
		$list = $this->getClassList();
		$result = array_search($className, $list);
		if ($result !== false) {
			unset($list[$result]);
			$this->setClassList($list);
		}
	}

	#endregion

	#region HtmlElementBase

	final public function path(): HtmlXPath
	{
		return new HtmlXPath($this->document, $this);
	}

	#endregion
}
