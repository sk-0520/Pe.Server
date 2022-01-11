<?php

declare(strict_types=1);

namespace PeServer\Core;

use \DOMElement;
use PeServer\Core\Throws\HtmlDocumentException;

/**
 * DOMElement のラッパー。
 */
class HtmlElement extends HtmlBase
{
	/**
	 * 生で使用する用。
	 *
	 * @var DOMElement
	 */
	public DOMElement $raw;
	public function __construct(HtmlDocument $document, DOMElement $raw)
	{
		parent::__construct($document, $raw);
		$this->raw = $raw;
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
				$value = '';
			} else {
				if ($this->raw->hasAttribute($qualifiedName)) {
					$this->raw->removeAttribute($qualifiedName);
				}
				return;
			}
		}

		$result = $this->raw->setAttribute($qualifiedName, $value);
		if ($result === false) { // @phpstan-ignore-line
			throw new HtmlDocumentException();
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
		if (StringUtility::isNullOrWhiteSpace($classValue)) {
			return [];
		}

		return StringUtility::split($classValue, ' ');
	}

	/**
	 * Undocumented function
	 *
	 * @param string[] $classNames
	 * @return void
	 */
	public function setClassList(array $classNames): void
	{
		$classValue = StringUtility::join($classNames, ' ');
		$this->setAttribute('class', $classValue);
	}

	public function addClass(string $className): void
	{
		$list = $this->getClassList();
		if (!ArrayUtility::contains($list, $className)) {
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
}
