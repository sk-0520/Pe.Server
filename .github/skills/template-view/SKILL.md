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
* TemplateEngineMigrationFactory, EngineMigrationTemplate が移行期間中の共存テンプレートとなる
* 置き換え
  1. Smarty テンプレート(View)を独自実装テンプレート(View)に置き換える
  2. React による UI 構築


## NodeTemplate

* 独自実装のテンプレートエンジン
* HTML 要素を愚直にツリーで表現する
* テンプレート独自のViewや構文は持たず PHP のみで構築される
* 基本的には簡単な HTML を生成し、複雑な UI は React などで構築する
  * ただし現在は過渡期の為、複雑な UI もテンプレート内で構築されることがある
