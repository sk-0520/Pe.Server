<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use Override;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\NodeBase;
use PeServer\Core\Regex;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * script/style など生テキスト要素の中身。
 *
 * エスケープせず出力するため、格納先の要素を終了させる記述や `<!--` を含む場合は例外とする。
 */
abstract class SourceNode extends NodeBase
{
	/**
	 * 生成。
	 *
	 * @param string $source
	 * @param non-empty-string $tagName 格納先のタグ名。
	 * @param Regex|null $regex 検査に使用する正規表現(null: 実行環境の文字コードで生成)。
	 */
	protected function __construct(
		public readonly string $source,
		string $tagName,
		?Regex $regex = null,
	) {
		$regex ??= new Regex();
		$pattern = '/<\/' . $regex->escape($tagName) . '|<!--/i';
		// @phpstan-ignore argument.type
		if ($regex->isMatch($source, $pattern)) {
			throw new ArgumentException('source');
		}
	}

	#region NodeBase

	#[Override]
	public function hasChildren(): bool
	{
		return !Text::isNullOrEmpty($this->source);
	}

	#[Override]
	public function toString(int $level, BuildOptions $buildOptions): string
	{
		return $this->source;
	}

	#endregion
}
