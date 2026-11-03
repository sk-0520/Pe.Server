# 作業メモ

このファイルは作業の引き継ぎ用。確定した設計方針は [テンプレート移植](./TEMPLATE-MIGRATION.md)、全体の入口は [AGENT.md](./AGENT.md) を参照する。記録は現在のコードより優先しない。

## 2026-11-03: エラー表示コンポーネントの Core 移動

* 移植版の [ShowErrorMessages](../PeServer/Core/Mvc/Template/Node/Html/Components/ShowErrorMessages/ShowErrorMessages.php) と [Props](../PeServer/Core/Mvc/Template/Node/Html/Components/ShowErrorMessages/ShowErrorMessagesProps.php) を App から Core の `Template/Node/Html/Components` へ移動し、DefaultPage の参照を更新した。Smarty プラグインと表示挙動は変更していない。
* 空・共通・項目別エラー表示のテストを追加し、トップページのテストと合わせて 6 テスト・77 assertions が成功した。
* 対象を絞った PHPStan 解析ではコードの診断はなかったが、全体設定の未使用 ignoreErrors 3 件により終了コードは失敗となった。

## 2026-11-03: TemplateEngine2 の調査

* 旧ブランチ先端 `66a83b11` の関連履歴を checkout せず確認した。詳細・コミット根拠は [テンプレート移植の履歴調査](./TEMPLATE-MIGRATION.md#templateengine2-の履歴調査)。
* 専用属性クラス導入後も、属性配列の契約は専用 shape と共通属性 alias の交差に依存していた。現在の型付き引数方式と同一視しない。
* PHPStan 2.3.1 の最小検証で、交差の片側の制約が落ちる問題を再現した。旧版に残る `lang => 0.1` の検出漏れに対応する結果。正否 15 ケースを照合したが、旧ブランチ全体のテストと IDE の再現は行っていない。
* プロダクトコード、旧ブランチ、ルート設定は変更していない。今回のリポジトリ変更はドキュメントのみ。

## 2026-11-03: NodeTemplate 移植

### 確認済み

* [DefaultPage](../PeServer/App/Views/Components/Pages/Default/DefaultPage.php) に本文の section、nav、アカウント別 header、footer、言語属性、見出し ID が実装された。管理者ログイン・開発環境の HTML 出力を利用者が提示して確認した。全状態の実動作確認が完了したという意味ではない。
* [IndexPage](../PeServer/App/Views/Pages/Home/IndexPage.php) の本文を [index.tpl](../PeServer/App/Views/Home/index.tpl) から移植した。利用者提示の開発環境 HTML でタイトル、本文、ビルド表、開発リンクの出力を確認した。
* [IndexPageTest](../test/PeServerUT/App/Views/Pages/Home/IndexPageTest.php) で開発・本番・テスト環境の本文を検証した。関連 Node テストとの合計で 131 テスト・228 assertions が成功した（記録時点）。
* 継承属性の重複は [HtmlGlobalAttributes::getNames](../PeServer/Core/Mvc/Template/Node/Html/Attribute/HtmlGlobalAttributes.php) 内で同名の最初のものを採用して解消した。子側優先で、子の値が null でも親へフォールバックしない。[ReflectionUtility::getAllProperties](../PeServer/Core/ReflectionUtility.php) は親子をまとめて取得する設計のため変更していない。
* [Element](../PeServer/Core/Mvc/Template/Node/Element.php) は子ノードの `hasChildren()` で描画を省略せず、子ノード自体を build するよう修正した。画像のみのリンクや子孫を持たない要素も消えないことを [ElementTest](../test/PeServerUT/Core/Mvc/Template/Node/ElementTest.php) で確認した。

### 次の候補

* index を本来の経路へ切り替える前に、動的なページで移植を検証する方針。Home の静的ページより Account を試金石にすることで利用者と合意した。
* 最初の候補は [login.tpl](../PeServer/App/Views/Account/login.tpl)。CSRF と input_helper、入力値の復元、共通／項目別エラー、環境限定フォームを含めて確認する。実装開始前に現在の移植状況を調べる。

### 保留・未確認

* 共通エラーの接続: 記録時点の DefaultPage は `parameter->values["error_messages"]` を渡している。旧版の `errors` と異なるため、データの生成元・抽出責務・画面挙動を含めて確認が必要。
* 項目別エラーのみの場合: 旧 Smarty プラグインはコード上では共通見出しを出すが、移植版は空の messages を非表示にする。利用者は実動作で確認するまで保留とした。仕様統一を勝手に行わない。
* `customTailNode` の差し込み方と使用有無は未確認。Props にあるという理由だけで移植完了とは扱わない。
* `temp_messages` の data 属性は現状維持の指示で差し戻した。型・用途・旧版との差が解決済みとは扱わない。
* index/index2 の経路切り替えは、この記録では未実施として扱う。作業前に現在のルートを確認する。
