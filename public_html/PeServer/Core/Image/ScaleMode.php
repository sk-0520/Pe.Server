<?php

declare(strict_types=1);

namespace PeServer\Core\Image;

class ScaleMode
{
	public const NEAREST_NEIGHBOUR = IMG_NEAREST_NEIGHBOUR;
	public const BILINEAR_FIXED = IMG_BILINEAR_FIXED;
	public const BICUBIC = IMG_BICUBIC;
	public const BICUBIC_FIXED  = IMG_BICUBIC_FIXED;
}
