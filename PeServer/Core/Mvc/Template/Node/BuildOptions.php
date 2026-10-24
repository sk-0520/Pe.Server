<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node;

readonly class BuildOptions
{
	#region define

	/** フォーマットなし */
	public const FORMAT_NONE = 0;
	/** 必要に応じて改行されるか */
	public const FORMAT_NEWLINE = 1;
	/** インデント処理されるか */
	public const FORMAT_INDENT = 2;

	#endregion

	public function __construct(
		/**
		 * フォーマットオプションのマスク
		 *
		 * @var int-mask-of<self::FORMAT_*>
		 * */
		public int $format,
		/**
		 * インデント文字列。
		 *
		 * @var string
		 */
		public string $indent
	) {
		//NOP
	}

	#region function

	public static function default(): self
	{
		return new self(
			format: self::FORMAT_NEWLINE | self::FORMAT_INDENT,
			indent: "\t"
		);
	}

	#endregion
}
