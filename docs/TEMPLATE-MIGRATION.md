# テンプレート移植

## 現在の構成

Web画面・HTMLエラー画面は NodeTemplate、メール本文8種類は Node 側の生成へ移行済み。Smarty の基盤・プラグイン・旧テンプレートは撤去済み。`TemplateFactory` は NodeTemplate を直接生成し、Core の `ViewActionResult` はコンポーネント名を渡す。移行用ルートと右上の識別表示 `2` も撤去済み。

## 現行ルート

| 画面 | 経路 |
|---|---|
| トップ | `/` |
| Home・Tool | `/about`、`/about/privacy`、`/about/contact`、`/api-doc`、`/dev/streaming`、`/tool`、`/tool/base64`、`/tool/json` |
| アカウント | `/account`、`/account/login`、`/account/signup`、`/account/user/...` |
| パスワード再設定 | `/password`、`/password/reminder`、`/password/reminding/{token}`、`/password/reset/{token}` |
| 公開プラグイン | `/plugin`、`/plugin/{plugin_id}` |
| 管理 | `/management`、`/management/control/...` |

画面内リンク・フォーム・成功時遷移・メール内 URL も正式経路を使う。移植済み画面に `2` 付きの互換経路を追加しない。業務 Logic、認可・CSRF ミドルウェア、共有 Ajax/API は移植だけを理由に変更しない。

## 設計上の決定

* テンプレートは PHP のノードツリーで構築し、独自のテンプレート構文を追加しない。再利用可能な `Core` は `App` に依存させない。詳細は [PHP スキル](../.github/skills/php/SKILL.md)を参照。
* 整形用の空白は出力しない。表示に必要な空白は明示的なテキストノードで、レイアウト間隔は CSS で表す。旧テンプレートの改行・空白に依存する箇所に注意する。
* `Element::build()` は属性・開始タグ・終了タグの生成を protected メソッドへ委譲する。自己終了要素は子を描画しない。継承先が描画処理を変更できる。
* メール HTML は Web 出力と分け、HTML 4.01 Strict の DOCTYPE を使う。メールモードではタグ名を大文字化し、空要素を `<BR>` 形式で出力する。属性・本文は変換しない。テキストメールは `TextBuilder` の text/break/Fragment で構造を明示し、HTML エスケープはしない。
* 意図した差分: 旧 `default.tpl` の `<!--nobanner-->` は復元しない。Matomo は本番環境のみ body 末尾で出力する。`temp_messages` の data 属性は従来の形を維持し、用途を確認せず変更しない。

## コンポーネントの注意

* `InputHelper` は TemplateParameter と key から値・項目別エラーを取得する。対応型を増やすときは型付き属性、描画、値復元、エラー表示、呼び出し側、テストを一緒に更新する。パスワードの再表示は Logic 側で空にする。
* `ShowErrorMessages` には対象キーだけでなく errors 全体と key を渡す。共通エラーと項目別エラーの表示契約を保つ。`errors` と `values` は別データである。
* Markdown は既存パーサーの結果を HTML として出力する。`safeMode` はサニタイズを保証しない。未信頼入力を未加工のまま RAW 出力しない。
* `Code` の未指定・未対応言語は旧ハイライターのフォールバックが未エスケープ。任意入力の汎用エスケープ部品として扱わない。
* `MarkdownEditor` は既存 JavaScript が使う ID・radio の name・`data-markdown-result` を維持する。現在は1ページ1個の利用を前提とする。
* `BotTextImage` は旧版の難読化を実装していない。移植による bot 対策の強化を前提にしない。

## 属性型の判断

現在の `HTMLxxxAttributes` は属性を型付き引数で受ける。これを配列 shape の交差型へ戻さない。過去の `TemplateEngine2` 調査では、閉じた shape 同士の交差で属性制約が失われる例を再現した。PHPStan 2.3.1 の独立設定では、開いた shape 同士なら値制約を保てたが、未知キーも受理する。共通属性・専用属性の型検証と未知キー拒否を両立できるかは未解決。

再検討時は、共通・専用属性双方の不正値、未知キー、IDE 補完、Props の具体型保持を確認する。PHPStan の更新を2026-12-05以降に再確認する目安とし、単独の閉じた shape で未知キーを拒否できるだけでは解決としない。

## 移植・検証

1. 既存ページ、呼び出し元、利用部品を調べて再利用し、レイアウトと本文を分ける。
2. URL、フォーム action、入力名、属性、環境別分岐、値復元、パスワード、共通／項目別エラーを確認する。
3. 成功・失敗後の再表示を含む関連テストを実行し、意図した差分を除いて DOM と挙動を比較する。空白や属性順の文字列一致は要求しない。
4. 画面移植とルート切り替えは別の変更として扱う。テスト成功と実ブラウザ確認を区別して記録する。

統合テスト基盤はミドルウェアを省略する。認可・CSRF の登録構成はルート単体テストで確認し、統合テストの成功を実効性確認とは扱わない。Windows での PHP 実行方法は[ローカル PHP スキル](../.github/skills/windows-local-php/SKILL.md)を参照。
