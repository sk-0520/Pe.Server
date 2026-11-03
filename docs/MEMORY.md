# 作業メモ

このファイルは作業の引き継ぎ用。確定した設計方針は [テンプレート移植](./TEMPLATE-MIGRATION.md)、全体の入口は [AGENT.md](./AGENT.md) を参照する。記録は現在のコードより優先しない。

## 2026-11-03: ユーザー登録の並行移植

* 登録開始・通知・本登録の Web 画面を [AccountSignupStep1Page](../PeServer/App/Views/Pages/Account/AccountSignupStep1Page.php)、[AccountSignupNotifyPage](../PeServer/App/Views/Pages/Account/AccountSignupNotifyPage.php)、[AccountSignupStep2Page](../PeServer/App/Views/Pages/Account/AccountSignupStep2Page.php) に移植した。`/account/signup2`、`/account/signup2/notify`、`/account/signup2/{token}` と対応する末尾 2 のアクションを追加した。元の signup 経路は維持した。
* 新系統の開始 POST だけ AccountSignupStep1Logic に signupPath を渡し、通知メール内の本登録 URL も signup2 に揃えた。未指定の旧系統は従来の signup のまま。NotLoginMiddleware、SignupStep2FilterMiddleware、POST の CsrfMiddleware は元経路と同じ構成で登録した。
* InputHelperType に Email・Password と対応する型付き属性による描画分岐を追加した。値の復元方針は既存と同じ。登録ステップ 2 のパスワードを空にする責務は AccountSignupStep2Logic に残した。
* Core に [BotTextImage](../PeServer/Core/Mvc/Template/Node/Html/Components/BotTextImage/BotTextImage.php) を追加し、旧 BotTextImageFunction の画像生成・寸法・文字サイズ・色・alt・class・data-hash を移植した。旧版で未実装の難読化は追加していない。画像リソースは finally で破棄する。
* メール受信案内の include は App の [EmailInformation](../PeServer/App/Views/Components/EmailInformation/EmailInformation.php) に移植した。旧 signup_step1.tpl の認証トークン欄の `<dd>` 閉じ忘れは、新ページのノード構造では正しく閉じる。
* 利用者の指示によりメール本文・メール用レイアウトは Smarty のまま残した。成功経路の統合テストで AppTemplate と EngineMigrationTemplate による拡張子の二重付与を検出し、[AppTemplate](../PeServer/App/Models/AppTemplate.php) から `.tpl` の付与を除去した。メール送信自体はテストでモックし、外部へ送信していない。
* 共通部品・アカウントページ・ルート設定の単体テスト 45 件・374 assertions、新旧の登録統合テスト 16 件・138 assertions が成功した。PNG の形式・寸法・hash、入力型・エスケープ、CSRF hidden、入力エラー、トークン再生成、メール内 URL、通知、メールアドレス不一致、登録・パスワードハッシュ・監査・ログイン状態を確認した。ミドルウェアは統合基盤で実行されないため、ルート単体テストで登録構成を確認した。実ブラウザ・実メール配信は未確認。
* 対象 PHPStan は、変更前から存在する AccountSignupStep1Logic の未使用 `$config`（property.onlyWritten）1 件と、既存の未使用 ignoreErrors 3 件で終了コードが失敗となった。新規部品の型指摘は修正済み。無関係な未使用プロパティや全体設定は変更していない。

## 2026-11-03: ログイン GET・POST の並行移植

* [AccountLoginPage](../PeServer/App/Views/Pages/Account/AccountLoginPage.php) を実装し、`/account/login2` の GET → `login_get2`、POST → `login_post2` から接続した。既存の `/account/login` の GET と POST は切り替えていない。
* 元 tpl と同じ入力属性、値の復元、項目別エラー、CSRF hidden 項目、開発環境限定フォームを構築した。入力項目の処理は現時点ではページ内の実装であり、汎用 InputHelper コンポーネントの移植完了とは扱わない。
* CSRF はページ内実装を撤去し、Core の [Csrf](../PeServer/Core/Mvc/Template/Node/Html/Components/Csrf/Csrf.php) と [Props](../PeServer/Core/Mvc/Template/Node/Html/Components/Csrf/CsrfProps.php) に移植した。Smarty と同じ `name`（既定の hidden input）・`id`（meta）を扱い、トークン未登録時は出力しない。トークン生成・POST 検証は既存処理の責務のまま。
* 通常・開発用フォームとも POST 先は `/account/login2`。POST に既存と同じ CsrfMiddleware を適用し、認証失敗時も AccountLoginPage で再表示する。成功時とログイン済み時のリダイレクトは従来と同じ。共通エラーの参照元と表示契約は下記のとおり修正済み。
* ページ単体の 4 ケースで開発・本番・テスト環境、CSRF の有無、値のエスケープ、項目別エラーを検証した（77 assertions）。新旧 GET とログイン済みリダイレクトの統合テストも 4 件成功した（15 assertions）。実ブラウザでの確認は未実施。
* POST 並行移植後、新旧ログイン関連の統合テスト 28 件・97 assertions、ページと CSRF の単体テスト 8 件・81 assertions が成功した。新経路での空入力再表示、匿名セッションの前提条件、認証成功、ログイン済みリダイレクトを含む。
* 利用者から login2 でエラーが表示されないとの報告を受け、DefaultPage の共通エラー参照を `parameter->errors[Validator::COMMON]` に修正した。空入力とパスワード不一致でエラー文言が表示される統合テストを追加し、修正前の 4 件失敗、修正後の新旧ログイン 29 件・116 assertions 成功を確認した。対象 PHPStan もエラーなし。
* 続いて利用者の指示で、ShowErrorMessagesProps を `errors` 全体と対象 `key` を受け取る契約に変更した。DefaultPage と AccountLoginPage の両方を更新し、対象キーの抽出をコンポーネント内へ集約した。項目別エラーのみでも共通見出しを出す旧版の挙動も復元した。単体 9 件・85 assertions、統合 29 件・116 assertions が成功。対象 PHPStan のコード診断はなく、既存の未使用 ignoreErrors 3 件のみで終了コードは失敗となった。

## 2026-11-03: ユーザー情報編集の並行移植

* 属性・Props の選択肢を文字列リテラル型にするか enum にするかの指針を、[テンプレート移植](./TEMPLATE-MIGRATION.md#属性props-の選択肢の型) に記録した。CsrfProps の判断を全体へ一般化しない。InputHelper の type は InputHelperType enum へ変更し、ページ・描画分岐・テストも更新した。将来の対応型追加時に必要な変更面も同文書に記載した。

* [AccountUserEditPage](../PeServer/App/Views/Pages/Account/AccountUserEditPage.php) を [user_edit.tpl](../PeServer/App/Views/Account/user_edit.tpl) から移植した。`/account/user/edit2` の GET・HEAD → `user_edit_get2`、POST → `user_edit_post2` を追加し、フォームの送信先も edit2 に揃えた。元の edit 経路は維持した。
* 既存の AccountUserEditLogic を再利用し、ユーザー権限フィルターと POST の CSRF ミドルウェアを元経路と同じ構成で登録した。成功時は従来どおり account/user へ遷移する。タブの DOM 構造、Markdown の初期プレビュー、更新先の data 属性、user_edit.js の読み込みも維持した。
* Core に [InputHelper](../PeServer/Core/Mvc/Template/Node/Html/Components/InputHelper/InputHelper.php) を追加した。TemplateParameter と対象 key から値を復元し、エラー class と項目別メッセージを付与する。今回使用する text・url・textarea と className・required・data・autoError に対応する部分移植であり、旧版の任意属性・全入力型の移植完了とは扱わない。ログインページの入力処理は今回切り替えていない。
* 旧 InputHelperFunction はコメント上の auto_error を実際には file で参照していた。新部品は型付きの autoError を使い、未指定は従来と同じ自動表示とした。旧プラグインは変更していない。
* Core に [Markdown](../PeServer/Core/Mvc/Template/Node/Html/Components/Markdown/Markdown.php) を追加し、既存の Core/Mvc/Markdown に処理を委譲した。class 補完、section ラッパー、変換済み HTML の出力を移植した。既存パーサーの safeMode はフラグを保持するだけであり、今回サニタイズを追加・保証したわけではない。
* 新旧経路の編集関連統合テスト 28 件・188 assertions が成功した。初期値、名前の必須・文字数、URL 不正、複数項目エラー、説明の上限超過、値の復元とプレビュー、更新成功・監査・DB 保存を確認した。実ブラウザでの確認は未実施。
* 共通部品・アカウントページ・ルート設定の単体テスト 38 件・252 assertions が成功した。統合テスト基盤は ItRouteWithoutMiddleware を使用するため、ミドルウェアは実行しない。ルート設定の単体テストで GET・HEAD・POST の接続と権限・CSRF の登録を別途確認した。対象 PHPStan はコード診断なし、既存の未使用 ignoreErrors 3 件のみ。

## 2026-11-03: Asset コンポーネントの Core 移動

* [Asset](../PeServer/Core/Mvc/Template/Node/Html/Components/Asset/Asset.php) と [Props](../PeServer/Core/Mvc/Template/Node/Html/Components/Asset/AssetProps.php) も App から Core の `Template/Node/Html/Components` へ移動し、DefaultPage の参照を更新した。移動に伴う表示挙動の変更はない。
* 追加したテストで、クエリ付き favicon の MIME が `application/octet-stream` になる既存挙動を発見した。今回の移動とは無関係のため変更していない。

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
* [login.tpl](../PeServer/App/Views/Account/login.tpl) の GET・POST 並行移植は上記のとおり実施済み。次は実ブラウザでの確認と、保留中の共通エラー表示を確認する。元の経路を勝手に切り替えない。

### 保留・未確認

* 共通エラーの接続は上記のとおり修正・統合テスト済み。実ブラウザでの修正後の表示確認は未実施。
* 項目別エラーのみの場合の共通見出しは、上記の全エラー受け渡しへの変更に伴い旧版に合わせて復元・単体テスト済み。実ブラウザでの確認は未実施。
* `customTailNode` の差し込み方と使用有無は未確認。Props にあるという理由だけで移植完了とは扱わない。
* `temp_messages` の data 属性は現状維持の指示で差し戻した。型・用途・旧版との差が解決済みとは扱わない。
* index/index2 の経路切り替えは、この記録では未実施として扱う。作業前に現在のルートを確認する。
