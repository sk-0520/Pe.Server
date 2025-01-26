<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attributes;

class HtmlAttributes extends Attributes
{
	/**
	 * 生成。
	 *
	 * @param array<string,int|bool|string> $attributes
	 * @phpstan-param HtmlTagAttributeAlias $attributes
	 */
	public function __construct(array $attributes = [])
	{
		/** @var array<string,string|null> */
		$attr = [];
		foreach ($attributes as $key => $value) {
			if ($key === "data") {
				assert(is_array($value)); // @phpstan-ignore function.alreadyNarrowedType,function.alreadyNarrowedType
				foreach ($value as $dataKey => $dataValue) {
					$attr["data-{$dataKey}"] = self::toValue($key, $dataValue);
				}
			} else {
				assert(!is_array(($value)));
				$attr[$key] = $this->toValue($key, $value);
			}
		}

		parent::__construct($attr);
	}

	#region function

	public function toValue(string $key, bool|int|string|null $value): string|null
	{
		if (is_int($value)) {
			return (string)$value;
		} elseif (is_bool($value)) {
			if ($key === 'translate') {
				return $value ? 'yes' : 'no';
			}
			return $value ? 'true' : 'false';
		}

		return $value;
	}

	#endregion
}
