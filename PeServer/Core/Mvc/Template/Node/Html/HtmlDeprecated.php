<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_CLASS | Attribute::TARGET_CLASS_CONSTANT)]
class HtmlDeprecated
{
	#region define

	/** 非標準 */
	public const REASON_NON_STANDARD = 1;
	/** 実験的 */
	public const REASON_EXPERIMENTAL = 2;
	/** 廃止予定 */
	public const REASON_OBSOLETE = 4;

	#endregion

	public function __construct(
		/** @phpstan-var int-mask-of<self::REASON_*> */
		public readonly int $reason,
		public readonly null|string $message = null
	) {
		//NOP
	}
}
