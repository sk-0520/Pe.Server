<?php

declare(strict_types=1);

namespace PeServer\Core\Version;

/**
 * .NET のバージョンクラスと同じ扱い。
 */
readonly class CliVersion
{
	/**
	 * 生成
	 *
	 * @param int $major
	 * @phpstan-param UnsignedIntegerAlias $major
	 * @param int $minor
	 * @phpstan-param UnsignedIntegerAlias $minor
	 * @param int $build
	 * @phpstan-param UnsignedIntegerAlias $build
	 * @param int $revision
	 * @phpstan-param -1|UnsignedIntegerAlias $revision
	 */
	function __construct(int $major, int $minor, int $build, int $revision)
	{
		$this->major = $major;
		$this->minor = $minor;
		$this->build = $build;
		$this->revision = $revision;
	}

	#region property

	/**
	 * [1] メジャー バージョン。
	 * @phpstan-var UnsignedIntegerAlias
	 */
	public int $major;
	/**
	 * [2] マイナー バージョン。
	 * @phpstan-var UnsignedIntegerAlias
	 */
	public int $minor;
	/**
	 * [3] ビルド バージョン。
	 * @phpstan-var UnsignedIntegerAlias
	 */
	public int $build;
	/**
	 * [4] リビジョン バージョン。
	 * @phpstan-var -1|UnsignedIntegerAlias
	 */
	public int $revision;

	#endregion

	#region function
	#endregion

	#region function
	#endregion
}
