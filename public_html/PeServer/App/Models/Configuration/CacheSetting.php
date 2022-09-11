<?php

declare(strict_types=1);

namespace PeServer\App\Models\Configuration;

use PeServer\App\Models\Configuration\PersistenceSetting;

/**
 * ストレージ設定。
 *
 * キャッシュという名前が悪いのはもういい。
 *
 * @immutable
 */
class CacheSetting
{
	#region variable

	/** 一時ディレクトリ */
	public string $temporary;
	/** DBキャッシュデータ配置ディレクトリ */
	public string $database;
	/** テンプレートキャッシュデータ配置ディレクトリ */
	public string $template;
	/** バックアップ配置ディレクトリ */
	public string $backup;
	// /** デプロイ諸々配置ディレクトリ */
	public string $deploy;

	#endregion
}
