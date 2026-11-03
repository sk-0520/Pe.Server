# テンプレート移植

## 採用方針と経緯

以下は 2026-11-03 までの利用者との会話で確認した方針。未検証の性能予測やエージェントの提案とは区別する。

* Smarty から PHP のノードツリーによる NodeTemplate へ移植し、その後 React による UI 構築へ進む。背景には devContainer でのファイル IO の遅さがあるが、NodeTemplate の方が常に高速であることを保証するものではない。
* 独自のテンプレート構文を追加せず、PHP のみで HTML を構築する。
* `HTMLxxxAttributes` は型検証のための意図的な設計。利用者は当初、グローバル属性とタグ専用属性を属性クラスなしで PHPStan に検証させようとしたが、両者を組み合わせたチェックを実現できず、現在の方式を採用した。試行錯誤は `TemplateEngine2` ブランチにあるとの利用者説明がある。履歴自体は本記録では未調査。
* View の冗長さ・Props の増加は主に App 側の責務として扱い、React 移行までのコストとして受け入れる。それだけを理由に Core を再設計しない。属性定義の凍結やコード生成の採用は決定されていない。
* 整形用のホワイトスペースを出さないのは意図した挙動。表示に必要な空白は明示的なテキストノード、レイアウト上の間隔は CSS で扱う。旧テンプレートの改行・空白への依存に注意する。
* 性能改善より移植と動作確認を先に進める。ブラウザの応答時間はアプリ全体と通信を含むため、テンプレート単体の速度として扱わない。

## 実装の入口

* [EngineMigrationTemplate](../PeServer/Core/Mvc/Template/EngineMigrationTemplate.php) はテンプレート名が `ComponentBase` の派生クラスなら NodeTemplate、それ以外なら SmartyTemplate を選ぶ。Node への移植だけで全ページが切り替わるわけではない。
* [DefaultPage](../PeServer/App/Views/Components/Pages/Default/DefaultPage.php) は共通レイアウト。[DefaultPageProps](../PeServer/App/Views/Components/Pages/Default/DefaultPageProps.php) の `children` が `<section id="content">` に入る。
* [IndexPage](../PeServer/App/Views/Pages/Home/IndexPage.php) は本文移植の例。ただしフォームの手本ではない。
* プラグインは [Smarty プラグイン群](../PeServer/Core/Mvc/Template/Plugin/) と [App のコンポーネント群](../PeServer/App/Views/Components/) を比較する。見た目だけでなく入力・出力の形と責務を確認する。
* `Core` は再利用可能なライブラリであり `App` に依存させない。詳細は [PHP スキル](../.github/skills/php/SKILL.md) に従う。

## default で意図した差分

* 旧 [default.tpl](../PeServer/App/Views/default.tpl) の `<!--nobanner-->` は無料サーバー時代の残骸であり不要。移植・復元しない。
* Matomo は旧版の「開発環境以外・head 内」から「本番環境のみ・body 末尾」へ意図的に変更した。互換性修正として戻さない。
* Asset のダンプコメントは開発環境の確認用途として許容されている。
* `temp_messages` の data 属性は変更を試した後、利用者の指示で `[$key => $value]` に戻した。旧版の `data-index` と同等であるとの合意ではない。今後の変更時は用途を確認する。

## エラー表示の注意点

* [ShowErrorMessagesFunction](../PeServer/Core/Mvc/Template/Plugin/ShowErrorMessagesFunction.php) が Smarty プラグイン、[ShowErrorMessages](../PeServer/App/Views/Components/ShowErrorMessages/ShowErrorMessages.php) と [Props](../PeServer/App/Views/Components/ShowErrorMessages/ShowErrorMessagesProps.php) が移植版。移植版が存在しないという前提で再実装しない。
* 旧版は全エラーから対象キーを抽出する。移植版の `messages` は `string[]` であり、渡された一覧を描画する。`key` は共通／項目別の表示を切り替える。
* [TemplateParameter](../PeServer/Core/Mvc/Template/TemplateParameter.php) の `errors` と `values` は別のデータ。呼び出し側の受け渡しまで調べる。表示コンポーネントだけの修正で移植完了としない。
* 項目別エラーしかない場合の共通見出しの挙動差は保留中。旧版コード上の分析と実際の画面確認を区別する。詳細は [MEMORY.md](./MEMORY.md)。

## 移植・検証の進め方

1. 元 tpl、利用プラグイン、対応コンポーネント、呼び出し元を読む。既存の部品を再利用する。
2. 共通レイアウトとページ本文を分け、URL、ラベル、属性、環境別分岐を維持する。意図した変更は上記の例外と区別する。
3. フォームは CSRF、入力属性、値の復元、パスワードの扱い、共通／項目別エラーを確認する。成功画面だけでなく失敗後の再表示と POST の検証も行う。
4. 開発・本番・テストの分岐を、関連する最小のテストで確認する。テスト成功と実ブラウザ確認を別々に記録する。
5. 意図した差分を除いて DOM 構造と挙動を比較する。HTML 文字列の空白や属性順の完全一致は要求しない。リンク間の空白など表示への影響は別途確認する。
6. ページ移植と本来のルートへの切り替えは別の変更として認識する。並行確認用の経路を勝手に廃止しない。

### Windows での検証

[ローカル PHP スキル](../.github/skills/windows-local-php/SKILL.md) に従う。`php-shell.bat` は自分のディレクトリへ移動するため、PHP へ渡すファイルと設定ファイルには絶対パスを使う。

PowerShell でリポジトリルートから、例えばトップページのテストを実行する場合:

```powershell
$root = (Get-Location).Path
$phpunit = (Get-ChildItem -LiteralPath "$root\test" -Filter 'phpunit.phar.*' -File | Select-Object -First 1).FullName
$env:APP_TEST_MODE = 'ut'
& "$root\dev\oreore\php-shell.bat" $phpunit --configuration "$root\dev\phpunit.xml" "$root\test\PeServerUT\App\Views\Pages\Home\IndexPageTest.php"
```

`APP_TEST_MODE` は [テストの bootstrap](../test/test.php) が要求する。同じシェル呼び出し内で設定する。関連 Node テストは [Node テストディレクトリ](../test/PeServerUT/Core/Mvc/Template/Node/) を指定できる。
