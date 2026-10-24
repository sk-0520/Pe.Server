<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html;

use Override;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ScriptType;
use PeServer\Core\Regex;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Throws\SerializeException;

/**
 * 実行されないデータブロック(JSON)。
 *
 * 既定のシリアライザーでは `<` `>` `&` `'` `"` が常にエスケープされる。
 * 注入されたシリアライザーの出力が script 要素を壊す場合は、`SourceNode` の検査で例外となる。
 */
class JsonDataNode extends ScriptSourceNode
{
	#region define

	/** script 要素へ埋め込むための既定のシリアライズ設定。 */
	private const DEFAULT_SAVE_OPTION = JsonSerializer::SAVE_HEX_TAG
		| JsonSerializer::SAVE_HEX_AMP
		| JsonSerializer::SAVE_HEX_APOS
		| JsonSerializer::SAVE_HEX_QUOT
		| JsonSerializer::SAVE_UNESCAPED_UNICODE;

	#endregion

	/**
	 * 生成。
	 *
	 * @param array<mixed>|object $value JSON として出力する値。
	 * @param JsonSerializer|null $serializer シリアライザー(null: `<` `>` `&` `'` `"` をエスケープする設定で生成)。
	 * @param Regex|null $regex 検査に使用する正規表現(null: 実行環境の文字コードで生成)。
	 * @throws SerializeException
	 */
	public function __construct(array|object $value, ?JsonSerializer $serializer = null, ?Regex $regex = null)
	{
		$serializer ??= new JsonSerializer(self::DEFAULT_SAVE_OPTION);
		parent::__construct($serializer->save($value)->toString(), $regex);
	}

	#region ScriptSourceNode

	#[Override]
	public function isAllowedType(ScriptType|null $type): bool
	{
		return $type === ScriptType::json || $type === ScriptType::importmap || $type === ScriptType::speculationrules;
	}

	#endregion
}
