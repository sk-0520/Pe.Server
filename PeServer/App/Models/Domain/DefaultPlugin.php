<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

/**
 * 標準プラグイン。
 */
readonly class DefaultPlugin
{
	private const DEFAULT_PROJECT_URL = 'https://github.com/sk-0520/Pe';
	private const DEFAULT_CHECK_URL = self::DEFAULT_PROJECT_URL . '/releases/download/<VERSION>';

	/**
	 * 生成。
	 *
	 * @param non-empty-string $pluginId
	 * @param non-empty-string $pluginName
	 * @param non-empty-string $checkUrl
	 * @param non-empty-string $projectUrl
	 * @param string[] $descriptions
	 * @param string[] $categories
	 * @param string $state
	 * @phpstan-param PluginState::* $state
	 */
	public function __construct(
		public string $pluginId,
		public string $pluginName,
		public string $checkUrl,
		public string $projectUrl,
		public array $descriptions,
		public array $categories,
		public string $state
	) {
	}

	#region function

	/**
	 * 一覧取得。
	 *
	 * @return self[]
	 */
	public static function get(): array
	{
		return [
			new self(
				'4524fc23-ebb9-4c79-a26b-8f472c05095e',
				'Pe.Plugins.DefaultTheme',
				//@phpstan-ignore-next-line
				'',
				self::DEFAULT_PROJECT_URL,
				['本体同梱標準テーマ。', 'ダウンロード先なし。',],
				['theme',],
				PluginState::ENABLED
			),
			new self(
				'67f0fa7d-52d3-4889-b595-be3703b224eb',
				'Pe.Plugins.Reference.ClassicTheme',
				self::DEFAULT_CHECK_URL . '/update-Pe.Plugins.Reference.ClassicTheme.json',
				self::DEFAULT_PROJECT_URL,
				['テーマの参考実装。', 'テーマをプラグインとして扱うのが💩と教えてくれた偉大なる参考実装。',],
				['theme',],
				PluginState::ENABLED
			),
			new self(
				'2e5c72c5-270f-4b05-afb9-c87f3966ecc5',
				'Pe.Plugins.Reference.Clock',
				self::DEFAULT_CHECK_URL . '/update-Pe.Plugins.Reference.Clock.json',
				self::DEFAULT_PROJECT_URL,
				['ランチャーボタン・ウィジェット・設定の参考実装。', '時計を表示する。',],
				['utility',],
				PluginState::ENABLED
			),
			new self(
				'799ce8bd-8f49-4e8f-9e47-4d4873084081',
				'Pe.Plugins.Reference.Eyes',
				self::DEFAULT_CHECK_URL . '/update-Pe.Plugins.Reference.Eyes.json',
				self::DEFAULT_PROJECT_URL,
				['ウィジェット・バックグラウンドの参考実装。', 'xeyes のおめめ。',],
				['toy',],
				PluginState::ENABLED
			),
			new self(
				'9dcf441d-9f8e-494f-89c1-814678bbc42c',
				'Pe.Plugins.Reference.FileFinder',
				self::DEFAULT_CHECK_URL . '/update-Pe.Plugins.Reference.FileFinder.json',
				self::DEFAULT_PROJECT_URL,
				['コマンド入力・設定の参考実装。', 'コマンド入力欄に入力された文字列をファイルパスとして扱う。',],
				['file', 'search',],
				PluginState::ENABLED
			),
			new self(
				'4fa1a634-6b32-4762-8ae8-3e1cf6df9db1',
				'Pe.Plugins.Reference.Html',
				self::DEFAULT_CHECK_URL . '/update-Pe.Plugins.Reference.Html.json',
				self::DEFAULT_PROJECT_URL,
				['WebView ウィジェットの参考実装。', '常に IME 死んでるマン。',],
				['utility',],
				PluginState::DISABLED
			),
		];
	}

	#endregion
}
