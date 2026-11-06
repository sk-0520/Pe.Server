# 作業メモ

作業の引き継ぎ事項を記録する。設計方針は[テンプレート移植](./TEMPLATE-MIGRATION.md)、入口と記録方針は[AGENT.md](./AGENT.md)を参照する。記録より現在のコードを優先する。

## 現状（2026-11-06）

* Web画面・HTMLエラー画面は NodeTemplate、メール本文8種類は Node 側の生成へ移行済み。Smarty 本体、アダプター、旧テンプレートは撤去済み。
* `TemplateFactory` は NodeTemplate を生成し、`ViewActionResult` は拡張子を付けずにコンポーネント名を渡す。
* 移行済み画面は正式 URL に一本化済み。移行用の `2` 付き URL・アクションと、右上の識別表示は撤去済み。現行経路は[テンプレート移植](./TEMPLATE-MIGRATION.md#現行ルート)を参照。
* ローカル SMTP トラップでメール8種類を受信確認済み。HTML6種類は HTML4.01 Strict の DOCTYPE、タグの大文字化、空要素の形式を確認した。メールクライアント全般の互換性を保証するものではない。

## 直近の検証

2026-11-05 の Smarty 撤去後、単体テスト31件・106 assertions、Home・Management・AccountSignup・AccountFamily・Password の統合テスト114件・776 assertions、PHPStan 全体が成功した。

## 未解決・確認時の注意

* 初回ログイン時に一度発生した匿名セッションの前提条件エラーは原因未確定。URL整理で解消したとは扱わない。
* Streaming の実ブラウザ動作は未確認。画面・API の統合テストも、統合基盤がミドルウェアを省略するため、認証・CSRF の実効性確認とは区別する。
* テンプレートの型設計と再調査条件は[テンプレート移植](./TEMPLATE-MIGRATION.md#属性型の判断)を参照。
