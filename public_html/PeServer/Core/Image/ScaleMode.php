<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

/**
 * 画像サイズ変換フラグ。
 */
abstract class ScaleMode
{
	#region define

	/** 最近接補間。 */
	public const NEAREST_NEIGHBOUR = IMG_NEAREST_NEIGHBOUR;
	/** 双直線補間の固定小数点実装 (デフォルト (画像作成時も含む))。  */
	public const BILINEAR_FIXED = IMG_BILINEAR_FIXED;
	/** 双三次補間。 */
	public const BICUBIC = IMG_BICUBIC;
	/** 双三次補間の固定小数点実装。 */
	public const BICUBIC_FIXED  = IMG_BICUBIC_FIXED;

	#endregion
}
