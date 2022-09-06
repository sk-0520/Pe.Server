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
	 * @param IDiContainer $container DIコンテナ。
	 */
	protected function __construct(
		/** @readonly */
		protected IDiContainer $container
	) {
	}
}
