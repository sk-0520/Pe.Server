<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\IDisposable;

/**
 * 限定的DIコンテナ。
 *
 * 生成元のデータを引き継ぎつつ生成元に影響を与えない。
 *
 * * 破棄処理は今回分のみ
 * * 未生成シングルトンは本処理で生成され、元コンテナでは生成されない
 *   * つまりは状態により元コンテナと差異が発生する可能性あり(ファクトリとかがその影響大)
 */
interface IScopedDiContainer extends IDiRegisterContainer, IDisposable
{
	//NONE
}
