<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

/**
 * 画像サイズ変換フラグ。
 */
enum ScaleMode: int
{
	#region define

	/** 最近接補間。 */
	case NearestNeighbour = IMG_NEAREST_NEIGHBOUR;
	/** 双直線補間の固定小数点実装 (デフォルト (画像作成時も含む))。  */
	case BilinearFixed = IMG_BILINEAR_FIXED;
	/** 双三次補間。 */
	case  Bicubic = IMG_BICUBIC;
	/** 双三次補間の固定小数点実装。 */
	case BicubicFixed = IMG_BICUBIC_FIXED;

	#endregion
}
