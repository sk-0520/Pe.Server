<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

/**
 * 標準プラグイン。
 */
readonly class DefaultPlugin
{
	/**
	 * 生成。
	 *
	 * @param non-empty-string $pluginId
	 * @param non-empty-string $pluginName
	 * @param non-empty-string $checkUrl
	 * @param non-empty-string $projectUrl
	 * @param string[] $descriptions
	 * @param string[] $categories
	 */
	public function __construct(
		public string $pluginId,
		public string $pluginName,
		public string $checkUrl,
		public string $projectUrl,
		public array $descriptions,
		public array $categories
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
				'https://github.com/sk-0520/Pe',
				['本体同梱標準テーマ。', 'ダウンロード先なし。',],
				['theme',]
			),
			new self(
				'67f0fa7d-52d3-4889-b595-be3703b224eb',
				'Pe.Plugins.Reference.ClassicTheme',
				'https://github.com/sk-0520/Pe/releases/download/<VERSION>/update-Pe.Plugins.Reference.ClassicTheme.json',
				'https://github.com/sk-0520/Pe',
				['テーマの参考実装。', 'テーマをプラグインとして扱うのが💩と教えてくれた偉大なる参考実装。',],
				['theme',],
			),
			new self(
				'2e5c72c5-270f-4b05-afb9-c87f3966ecc5',
				'Pe.Plugins.Reference.Clock',
				'https://github.com/sk-0520/Pe/releases/download/<VERSION>/update-Pe.Plugins.Reference.Clock.json',
				'https://github.com/sk-0520/Pe',
				['ランチャーボタン・ウィジェット・設定の参考実装。', '時計を表示する。',],
				['utility',],
			),
			new self(
				'799ce8bd-8f49-4e8f-9e47-4d4873084081',
				'Pe.Plugins.Reference.Eyes',
				'https://github.com/sk-0520/Pe/releases/download/<VERSION>/update-Pe.Plugins.Reference.Eyes.json',
				'https://github.com/sk-0520/Pe',
				['ウィジェット・バックグラウンドの参考実装。', 'xeyes のおめめ。',],
				['toy',],
			),
			new self(
				'9dcf441d-9f8e-494f-89c1-814678bbc42c',
				'Pe.Plugins.Reference.FileFinder',
				'https://github.com/sk-0520/Pe/releases/download/<VERSION>/update-Pe.Plugins.Reference.FileFinder.json',
				'https://github.com/sk-0520/Pe',
				['コマンド入力・設定の参考実装。', 'コマンド入力欄に入力された文字列をファイルパスとして扱う。',],
				['file', 'search',],
			),
			new self(
				'4fa1a634-6b32-4762-8ae8-3e1cf6df9db1',
				'Pe.Plugins.Reference.Html',
				'https://github.com/sk-0520/Pe/releases/download/<VERSION>/update-Pe.Plugins.Reference.Html.json',
				'https://github.com/sk-0520/Pe',
				['WebView ウィジェットの参考実装。', '常に IME 死んでるマン。',],
				['utility',],
			),
		];
	}

	#endregion
}
