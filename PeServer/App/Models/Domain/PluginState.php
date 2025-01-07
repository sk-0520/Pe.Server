<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

use PeServer\Core\I18n;
use PeServer\Core\I18nProperty;
use PeServer\Core\Throws\NotImplementedException;

abstract class PluginState
{
	/** 予約済み */
	public const RESERVED = 'reserved';
	/** 有効 */
	public const ENABLED = 'enabled';
	/** 無効 */
	public const DISABLED = 'disabled';
	/** これなぁ、どうしようかなぁ */
	public const CHECK_FAILED = 'check_failed';

	#region function

	/**
	 * 一覧取得。
	 *
	 * @return list<PluginState::*>
	 */
	public static function getItems(): array
	{
		return [
			PluginState::RESERVED,
			PluginState::DISABLED,
			PluginState::ENABLED,
			PluginState::CHECK_FAILED,
		];
	}

	/**
	 * 選択可能一覧取得。
	 *
	 * @return list<PluginState::*>
	 */
	public static function getEditableItems(): array
	{
		return [
			PluginState::DISABLED,
			PluginState::ENABLED,
		];
	}

	public static function toString(string $userLevel): string
	{
		return match ($userLevel) {
			self::RESERVED => I18n::message('enum/plugin_state/reserved'),
			self::ENABLED => I18n::message('enum/plugin_state/enabled'),
			self::DISABLED => I18n::message('enum/plugin_state/disabled'),
			self::CHECK_FAILED => I18n::message('enum/plugin_state/check_failed'),
			default => throw new NotImplementedException()
		};
	}

	#endregion
}
