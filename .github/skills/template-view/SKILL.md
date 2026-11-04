---
name: template-view
applyTo: "**/*.php"
description: "Pe.Server プロジェクトでのテンプレートエンジンの実装、修正、デバッグ、テスト、View構築を行うときに使用する。プロジェクト固有の方針、Core/App の依存関係を確認する。"
---

# テンプレートエンジン

* エンジンは `PeServer\Core\Mvc\Template` が中心となる
* `ITemplateFactory` 実装がテンプレート自体を生成し、 `ITemplate` 実装がテンプレートのビルドを行う

## 開発過渡期

* SmartyTemplate(Smarty) と NodeTemplate(独自実装) が共存している状態で開発が進んでいる
* 元々 Smarty を使用していたが、 PHP 自体を devContainer で開発するとファイル IO が遅くなるため、独自実装のに舵を切った背景がある
* EngineMigrationTemplate が移行期間中の共存テンプレートとなる
* 未移植画面を並行移植するときは、新しい NodeTemplate 側のルート・アクションに `2` を付与し、旧 Smarty 側を維持して新旧比較できる状態にする。画面内リンク・フォーム送信先・成功時遷移も対応する系統へ揃える。
* トップ・account・password・plugin は利用者の承認により正式 URL `/`、`/account/...`、`/password/...`、`/plugin/...` の NodeTemplate 版へ一本化済み。この範囲の `2` 付き URL・アクション・開発用リンクは撤去した。右上の `2`（`id="node-template-indicator"`）は E2E で NodeTemplate を識別するため共通の DefaultPage に復旧し、環境を問わず表示する。互換経路は残さず、送信済みの `2` 付きメールリンクも利用できない。新たに生成するリンク・フォーム・メール URL は正式 URL を使う。Home の案内・API・開発用 Streaming と Tool 画面も既存 URL で NodeTemplate へ移行済み。共有 Ajax/API の実装と Smarty のメール本文は変更しない。詳細は [テンプレート移植](../../../docs/TEMPLATE-MIGRATION.md) を参照する。
* 既存 URL の切り替えは利用者が明示的に指示した範囲に限定する。特定の画面群への切り替え指示を、他の画面群へ適用しない。
* 管理画面も利用者の移行指示により `/management/...` と `/management/control/...` の既存 URL で NodeTemplate へ切り替え済み。全 20 画面で右上の `2` を表示する。業務 Logic・認可／CSRF 構成・Ajax/API・ダウンロードは維持し、管理画面に `2` 付き URL は追加しない。メール本文と Web エラー画面は引き続き Smarty。
* 置き換え
  1. Smarty テンプレート(View)を独自実装テンプレート(View)に置き換える
  2. React による UI 構築


## NodeTemplate

* 独自実装のテンプレートエンジン
* HTML 要素を愚直にツリーで表現する
* テンプレート独自のViewや構文は持たず PHP のみで構築される
* 基本的には簡単な HTML を生成し、複雑な UI は React などで構築する
  * ただし現在は過渡期の為、複雑な UI もテンプレート内で構築されることがある
