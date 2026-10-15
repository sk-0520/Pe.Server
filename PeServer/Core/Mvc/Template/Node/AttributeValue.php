<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

/**
 * 未指定属性の型分離用定義。
 *
 * null が有効な云々になると面倒なので未指定を明示するために使用。
 * ユーザーコードではなくコンストラクタのデフォルト引数で使用されることを想定している。
 */
enum AttributeValue
{
	/**
	 * 未指定。
	 */
	case Unspecified;
}
