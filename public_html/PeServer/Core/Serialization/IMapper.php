<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use PeServer\Core\Throws\MapperKeyNotFoundException;
use PeServer\Core\Throws\MapperTypeException;

/**
 * 配列とオブジェクトの相互変換。
 *
 * 細かい制御が必要な場合は `Mapping` 属性を使用する想定(実装クラスがサポートしていればになるが)。
 *
 * * 配列とクラスの設計は十分に制御可能なデータであることが前提(=開発者が操作可能)
 * * **!!現状相互ではない!!**
 *   * 相互にする気もない
 */
interface IMapper
{
	#region function

	/**
	 * 配列データをオブジェクトにマッピング。
	 *
	 * @param array<string,mixed> $source 元データ。
	 * @param object $destination マッピング先
	 * @throws MapperKeyNotFoundException キーが見つからない(`Mapping::FLAG_EXCEPTION_NOT_FOUND_KEY`)。
	 * @throws MapperTypeException 型変換がもう無理(`Mapping::FLAG_EXCEPTION_TYPE_MISMATCH`)。
	 */
	function mapping(array $source, object $destination): void;

	/**
	 * オブジェクトデータを配列に変換。
	 *
	 * @param object $source
	 * @return array<string,mixed>
	 */
	function export(object $source): array;

	#endregion
}
