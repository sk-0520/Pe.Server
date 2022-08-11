<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use \Attribute;

/**
 * 注入設定。
 *
 * * コンストラクタ: 割り当ての型を強制。
 * * メンバ変数: 生成後に割り当て処理を実施。
 *
 * @immutable
 */
#[Attribute]
class Inject
{
	/**
	 * 生成。
	 *
	 * @param string $id 優先ID。コンストラクタ設定時は必須。
	 */
	public function __construct(
		public string $id = ''
	) {
	}
}
