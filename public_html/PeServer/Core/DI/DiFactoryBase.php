<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

/**
 * DIコンテナを用いたファクトリパターン基底処理。
 *
 * 基底っていうかメンバ変数だけ保持するだけ。
 */
abstract class DiFactoryBase
{
	/**
	 * 生成。
	 *
	 * 継承した側は `DiFactoryTrait` を用いる想定。
	 *
	 * @param IDiContainer $container DIコンテナ。
	 */
	protected function __construct(
		protected readonly IDiContainer $container
	) {
	}
}
