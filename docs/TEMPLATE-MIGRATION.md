# テンプレート移植

## 採用方針と経緯

以下は 2026-11-03 までの利用者との会話で確認した方針。未検証の性能予測やエージェントの提案とは区別する。

* Smarty から PHP のノードツリーによる NodeTemplate へ移植し、その後 React による UI 構築へ進む。背景には devContainer でのファイル IO の遅さがあるが、NodeTemplate の方が常に高速であることを保証するものではない。
* 独自のテンプレート構文を追加せず、PHP のみで HTML を構築する。
* `HTMLxxxAttributes` は型検証のための意図的な設計。利用者は当初、グローバル属性とタグ専用属性を属性クラスなしで PHPStan に検証させようとしたが、両者を組み合わせたチェックを実現できず、現在の方式を採用した。`TemplateEngine2` の履歴では、属性クラス導入後も配列の交差型で検証を試している。以下の履歴調査と再現結果を参照。
* View の冗長さ・Props の増加は主に App 側の責務として扱い、React 移行までのコストとして受け入れる。それだけを理由に Core を再設計しない。属性定義の凍結やコード生成の採用は決定されていない。
* 整形用のホワイトスペースを出さないのは意図した挙動。表示に必要な空白は明示的なテキストノード、レイアウト上の間隔は CSS で扱う。旧テンプレートの改行・空白への依存に注意する。
* 性能改善より移植と動作確認を先に進める。ブラウザの応答時間はアプリ全体と通信を含むため、テンプレート単体の速度として扱わない。

## TemplateEngine2 の履歴調査

2026-11-03 にローカルの `TemplateEngine2`（先端 `66a83b1188d80e238a9877338144b7191dc7893c`）を調査した。`TemplateEngine3` との共通祖先は `120ad838`、そこから旧ブランチ先端までは 81 コミット。checkout はせず、関連ファイルの履歴と差分を読んだ。

### 型付きの配列から専用クラスへの試行

| コミット | ソースから確認した変更 |
|---|---|
| `1e5c5e52` | `Tag::html()` に属性配列を直接渡す構造。共通属性を `HtmlTagAttributeAlias` に定義し、当初は `array{class?:string,id?:string,name?:string}&array<string,string>` としていた。 |
| `89659833` | 共通属性から汎用 `array<string,string>` との交差を外す。`Attr::html()` 側では `array{}&HtmlTagAttributeAlias` を使用。 |
| `b35b0029` | 共通属性 shape を `lang`、`data`、bool 属性等へ拡充。`HtmlAttributes` で文字列化と `data-*` 展開を実装。 |
| `a1180952` | `Attr` を削除し、専用 `HTMLHtmlAttributes` を導入。ただしコンストラクタは `array` のままで、検証は `array{xmlns?: non-empty-string}&HtmlTagAttributeAlias` に依存。 |
| `698e5a65` | 多数の専用 Element の引数を子ノード配列から `HtmlContent` 等へ変更。単に属性だけでなく、ツリーの組み立て方も試行していた。 |
| `c4866f4c` | タグの Element から Props を取り除き、ComponentBase 側へ分離。HTML 属性とコンポーネント用 Props の責務を分けた。 |
| `d064d2cc` → `05eefa7d` | Layout/Props を追加し、コンストラクタのテンプレート型で Props を扱う案から、Layout に具体的な `LayoutProps` を再宣言する案へ変更。後者には `"lang" => 0.1` が残り、コミットメッセージにも PHPStan が属性を検知しない旨がある。 |
| `66a83b11` | 型エイリアス名を変更。配列 shape の交差による属性検証も `"lang" => 0.1` も残っている。 |

特に確認する過去ファイル（パスは当該コミットのもの。現ブランチの同名クラスと混同しない）:

* `PeServer/Core/Mvc/Template/Node/Html/Attribute/HTMLHtmlAttributes.php`
* `PeServer/Core/Mvc/Template/Node/Html/Attribute/HTMLAnchorAttributes.php`
* `PeServer/Core/Mvc/Template/Node/Html/Tag.php`
* `PeServer/Core/Mvc/Template/Node/Component/Layout/Layout.php`
* `PeServer/Core/Mvc/Template/Node/ComponentBase.php`
* `dev/phpstan.neon`

旧版も既に `Tag::a(HTMLAnchorAttributes, HtmlContent): HTMLAnchorElement` のような専用 Element/属性クラス構成だった。ただし「クラスが存在すること」と「属性名・値を PHP の引数型で検証できること」は別である。

### 属性の交差型の再現確認

手元の PHPStan 2.3.1、PHP 対象 8.4、level 8 の独立した最小設定で、旧版の共通属性 alias と交差の形を切り出して解析した。これは旧ブランチ全体のテストや、当時使用していたツール環境の完全再現ではない。

```php
/** @phpstan-param array{xmlns?: non-empty-string}&globa-alias-html-tag-attribute $attributes */
public function __construct(array $attributes = [])
```

共通属性 alias は `lang?: non-empty-string` を含む。しかしこの交差型を `PHPStan\dumpType` で確認すると、推論結果は `array{xmlns?: non-empty-string}` になり、`lang` の制約が落ちた。

| 検証する引数型 | `lang => 0.1` | `xmlns => 0.1` |
|---|---|---|
| 共通属性 alias 単体 | 型エラー | 対象外 |
| 専用 shape `&` 共通属性 alias（旧版の順序） | **引数型エラーなし** | 型エラー |
| 共通属性 alias `&` 専用 shape（順序を逆転） | 型エラー | **引数型エラーなし** |
| 一つの shape に `lang` と `xmlns` を列挙 | 型エラー | 型エラー |

* より小さい `array{lang?: non-empty-string}&array{xmlns?: non-empty-string}` でも推論結果が左側だけになることを確認した。単なる alias の名称間違いとは切り分けられる。
* 一つにまとめた shape でも、今回の検証では未知キー `langg` を引数エラーとして検出しなかった。既知キーの値検証と、未知キーの拒否は別の要件。
* 対照として enum 型の `lang` 引数を持つクラスでは、`lang: 0.1` と未知の名前付き引数 `langg:` の両方を検出した。現在の設計に近い検証境界である。
* 正常値も含む 15 ケースの引数診断を照合した。意図的な不正入力と `dumpType` を含む検証なので、「PHPStan 全体がエラーなし」という意味ではない。
* `globa-alias-*` は旧版に残る実際の綴りで、`global` の末尾 `l` が抜けた typo。履歴の引用・再現ではそのまま用いた。履歴は変更していない。

### 現方式を評価するときの注意

* 現在の [HTMLHtmlAttributes](../PeServer/Core/Mvc/Template/Node/Html/Attribute/HTMLHtmlAttributes.php) は、配列 shape の交差ではなく `Bcp47|null $lang` 等の型付き引数で入力を受ける。PHPStan の検証に加えて PHP 自体の引数契約が働く。
* 一方、[HtmlBuilder](../PeServer/Core/Mvc/Template/Node/Html/HtmlBuilder.php) は多くのタグで共通の `HtmlElement` を生成する。旧版の専用 Element 群をそのまま維持しているわけではない。
* 型付き Props の伝播も課題だった。現在の [ComponentBase](../PeServer/Core/Mvc/Template/Node/ComponentBase.php) はクラスレベルの `TProps` を持つ。旧版の IDE 上の不具合そのものは再現しておらず、コミットの記述とコード上の変更を根拠としている。
* したがって「属性クラスをなくして交差型に戻せば簡単」という提案は、過去の検出漏れを再び持ち込む可能性がある。再検討時は **共通属性と専用属性双方の不正値、未知キー、IDE 補完、Props の具体型保持** を検証してから判断する。
* この結果は「PHPStan の交差型が一般に使えない」という主張ではない。ここで使った optional な array shape の組み合わせと、上記ツール条件で確認した限界である。

## 実装の入口

* [EngineMigrationTemplate](../PeServer/Core/Mvc/Template/EngineMigrationTemplate.php) はテンプレート名が `ComponentBase` の派生クラスなら NodeTemplate、それ以外なら SmartyTemplate を選ぶ。Node への移植だけで全ページが切り替わるわけではない。
* [DefaultPage](../PeServer/App/Views/Components/Pages/Default/DefaultPage.php) は共通レイアウト。[DefaultPageProps](../PeServer/App/Views/Components/Pages/Default/DefaultPageProps.php) の `children` が `<section id="content">` に入る。
* [IndexPage](../PeServer/App/Views/Pages/Home/IndexPage.php) は本文移植の例。ただしフォームの手本ではない。
* プラグインは [Smarty プラグイン群](../PeServer/Core/Mvc/Template/Plugin/)、[Core の HTML コンポーネント群](../PeServer/Core/Mvc/Template/Node/Html/Components/)、[App のコンポーネント群](../PeServer/App/Views/Components/) を比較する。見た目だけでなく入力・出力の形と責務を確認する。
* `Core` は再利用可能なライブラリであり `App` に依存させない。詳細は [PHP スキル](../.github/skills/php/SKILL.md) に従う。

## default で意図した差分

* 旧 [default.tpl](../PeServer/App/Views/default.tpl) の `<!--nobanner-->` は無料サーバー時代の残骸であり不要。移植・復元しない。
* Matomo は旧版の「開発環境以外・head 内」から「本番環境のみ・body 末尾」へ意図的に変更した。互換性修正として戻さない。
* [Asset](../PeServer/Core/Mvc/Template/Node/Html/Components/Asset/Asset.php) と [Props](../PeServer/Core/Mvc/Template/Node/Html/Components/Asset/AssetProps.php) は Core 所属。ダンプコメントは開発環境の確認用途として許容されている。
* `temp_messages` の data 属性は変更を試した後、利用者の指示で `[$key => $value]` に戻した。旧版の `data-index` と同等であるとの合意ではない。今後の変更時は用途を確認する。

## エラー表示の注意点

* [ShowErrorMessagesFunction](../PeServer/Core/Mvc/Template/Plugin/ShowErrorMessagesFunction.php) が Smarty プラグイン、[ShowErrorMessages](../PeServer/Core/Mvc/Template/Node/Html/Components/ShowErrorMessages/ShowErrorMessages.php) と [Props](../PeServer/Core/Mvc/Template/Node/Html/Components/ShowErrorMessages/ShowErrorMessagesProps.php) が移植版。いずれも Core 所属。移植版が存在しないという前提で再実装しない。
* 旧版・移植版とも全エラーから対象キーを抽出する。移植版には `errors`（`array<string,string[]>`）全体と対象 `key` を渡す。呼び出し側で対象キーの一覧に絞り込まない。
* [TemplateParameter](../PeServer/Core/Mvc/Template/TemplateParameter.php) の `errors` と `values` は別のデータ。呼び出し側の受け渡しまで調べる。表示コンポーネントだけの修正で移植完了としない。
* 共通表示では全エラーが空なら何も出さず、項目別エラーしかない場合も共通見出しを出す。共通メッセージが空ならリストは出さない。旧版に合わせた挙動を単体テストで検証済み。実際の画面確認とは区別する。詳細は [MEMORY.md](./MEMORY.md)。

## 移植・検証の進め方

### 属性・Props の選択肢の型

* PHPStan の文字列リテラル型と enum は一律にどちらかへ統一しない。ファイル IO に伴う読み込み負荷、既定引数でカバーできる利用、継承などでの共通性、呼び出し頻度、IDE 補完の使いやすさを踏まえて選ぶ。コード量や利用範囲の変化に応じて見直す。
* [CsrfProps](../PeServer/Core/Mvc/Template/Node/Html/Components/Csrf/CsrfProps.php) の `'name'|'id'` は範囲が閉じ、原則として既定引数で利用できるため、型定義用ファイルの追加読み込みを避ける選択とした。enum 全般を避ける指示ではない。
* クラス継承などで共通項となるものや呼び出しの多いものは、IDE 支援も考慮して enum などで定義してよい。[InputHelperType](../PeServer/Core/Mvc/Template/Node/Html/Components/InputHelper/InputHelperType.php) はその対象。HTML の input type と違って textarea も含むため、既存 InputType とは別の型にする。
* 将来 InputHelper の対応型を増やす際は、enum のケースだけでなく型付き属性との対応、描画分岐、値の復元、項目別エラー、呼び出し側とテストを一緒に拡張する。未対応型を先に列挙しない。

### 入力と Markdown の共通部品

* [InputHelper](../PeServer/Core/Mvc/Template/Node/Html/Components/InputHelper/InputHelper.php) は TemplateParameter と key を受け取り、値の復元・エラー class・ShowErrorMessages による項目別表示をまとめる。現時点の対応型は text・url・textarea。任意属性や他の入力型が必要になったら、型付き属性による検証を維持して拡張する。旧プラグインの属性配列へ戻さない。
* [Markdown](../PeServer/Core/Mvc/Template/Node/Html/Components/Markdown/Markdown.php) は MarkdownFunction の移植版。既存パーサーの結果を HTML として section 内へ出力する。safeMode の指定は既存パーサーへ渡すが、既存実装にはサニタイズの保証はない。単なる TextNode への入力を未変換のまま RAW 出力してはならない。

### 手順

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
