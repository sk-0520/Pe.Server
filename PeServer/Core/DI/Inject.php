<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use Attribute;
use PeServer\Core\Text;

/**
 * 注入設定。
 *
 * * コンストラクタ: 割り当ての型を強制。
 * * メンバ変数: 生成後に割り当て処理を実施。
 *   * readonly プロパティが初期化済みの場合、再代入によりエラーとなる。
 */
#[Attribute]
readonly class Inject
{
	/**
	 * 生成。
	 */
	public function __construct(
		/** @var string|class-string $id 優先ID。コンストラクタ設定時は必須。 */
		public string $id = Text::EMPTY
	) {
		//NOP
	}
}
