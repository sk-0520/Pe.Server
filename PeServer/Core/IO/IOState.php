<?php

declare(strict_types=1);

namespace PeServer\Core\IO;

use DateTimeImmutable;
use PeServer\Core\Utc;

/**
 * `stat` でもらえる情報。
 */
readonly class IOState
{
	/**
	 * 生成。
	 *
	 * @param int $deviceNumber デバイス番号
	 * @param int $inode inode 番号
	 * @param int $mode inode プロテクトモード
	 * @param int $linkCount リンク数
	 * @param int $userId 所有者のユーザー ID
	 * @param int $groupId 所有者のグループ ID
	 * @param int $deviceType inode デバイス の場合、デバイスの種類
	 * @param int $size バイト単位のサイズ
	 * @param DateTimeImmutable $accessDateTime 最終アクセス時間
	 * @param DateTimeImmutable $updatedDateTime 最終修正時間
	 * @param DateTimeImmutable $createdDateTime 最終 inode 変更時間
	 * @param int $blockSize ファイル IO のブロックサイズ
	 * @param int $blockCount 512 バイトのブロックの確保数
	 */
	public function __construct(
		public int $deviceNumber,
		public int $inode,
		public int $mode,
		public int $linkCount,
		public int $userId,
		public int $groupId,
		public int $deviceType,
		public int $size,
		public DateTimeImmutable $accessDateTime,
		public DateTimeImmutable $updatedDateTime,
		public DateTimeImmutable $createdDateTime,
		public int $blockSize,
		public int $blockCount
	) {
	}

	#region function

	/**
	 * `stat` 的なものから作成。
	 *
	 * @param array<string|int,int> $values
	 * @return self
	 */
	public static function createFromStat(array $values): self
	{
		return new IOState(
			$values['dev'],
			$values['ino'],
			$values['mode'],
			$values['nlink'], //cspell:disable-line
			$values['uid'],
			$values['gid'],
			$values['rdev'], //cspell:disable-line
			$values['size'],
			Utc::toDateTimeFromUnixTime($values['atime']),
			Utc::toDateTimeFromUnixTime($values['mtime']),
			Utc::toDateTimeFromUnixTime($values['ctime']),
			$values['blksize'], //cspell:disable-line
			$values['blocks']
		);
	}

	#endregion
}
