# 作業メモ

このファイルは作業の引き継ぎ用。確定した設計方針は [テンプレート移植](./TEMPLATE-MIGRATION.md)、全体の入口は [AGENT.md](./AGENT.md) を参照する。記録は現在のコードより優先しない。

## 2026-11-05: Smarty依存の撤去

* 利用者によるSmarty本体削除に続き、コード側の専用プラグイン・TemplateStore・SmartyTemplate・EngineMigrationTemplate・旧メール生成メソッドと旧テンプレート33枚を撤去。オートロードとPHPStanの依存登録も削除した。TemplateFactoryはNodeTemplateを直接生成し、CoreのViewActionResultは拡張子を付けずにコンポーネント名を渡す。
* 利用者が全メール8種類をローカルSMTPトラップで受信済み。HTML6通を復号して全321タグの大文字化、HTML4 StrictのDOCTYPEと空要素形式を確認。テキストメールはTextBuilderでラベル・値・改行・任意コメント節を明示し、単体テストで完全一致を確認した。
* 撤去後の検証は単体31件・106 assertions、Home・Management・AccountSignup・AccountFamily・Passwordの統合114件・776 assertions、PHPStan全体が成功。アプリケーションのコード・テスト・PHPStan設定にSmarty参照は残らず、旧テンプレートも残存なし。利用者のElementTest変更は維持した。

## 2026-11-05: 全メールの送信本文をNode生成へ切替

* 登録・パスワード再設定に続き、メール変更3種類とフィードバック／クラッシュレポートの送信本文を `createNodeMailTemplate()` に切り替えた。Appから旧 `createMailTemplate()` への呼び出しはなくなった。Smarty基盤・旧テンプレートの撤去は未実施。
* メール変更の統合テスト2件・34 assertions、全メール生成の単体テスト5件・65 assertions、PHPStan全体が成功。API通知メールは生成単体テストによる確認であり、実送信やAPI送信経路の統合確認は未実施。

## 2026-11-04: 登録メールをNodeTemplateへ切替

* `AppTemplate::createNodeMailTemplate()` と `AppEmailTemplateBuilder` による生成を維持し、アカウント登録のStep1確認URLメール・Step2完了通知の送信をNodeTemplateへ切り替えた。他のメールは未切替。HTMLメール6種は `TextNode`・HTML_EMAIL Builder、フィードバック／クラッシュレポートのテキスト2種は `TextBuilder` で生成する。
* AccountSignup統合テスト8件・72 assertions、メール生成単体5件・65 assertions、PHPStan全体が成功。統合テストではMailerをモックし、Node本文のDOCTYPE・style・URL／ログインIDを確認する。後日、利用者がローカルSMTPトラップで確認URL通知と登録完了通知を受信し、確認URLから登録フォームを表示、登録完了後のログイン状態を確認した。実メールクライアント全般の互換性を保証するものではない。

## 2026-11-04: Home・Tool・エラー画面の NodeTemplate 移行

* 利用者の指示で Home の案内・API・開発用 Streaming と Tool の全画面を既存 URL のまま NodeTemplate に切り替えた。フォーム action・入力名・値復元・エラー表示、業務 Logic、Streaming 用 Ajax 経路は維持し、旧 Home・Tool Smarty テンプレート 8 ファイルを削除した。
* Home・Tool の統合テスト 30 件・178 assertions が成功。実ブラウザでの Streaming 動作は未確認。Smarty のメール本文は引き続き Smarty。管理画面の旧 `.tpl` は比較用に残っている。
* AppErrorHandler の HTML エラー応答も ErrorPage へ切り替え、旧 `error.tpl` を削除した。エラー番号・メッセージ・ステータスを NodeTemplate で描画し、既定メッセージとテキストエスケープを単体 2 件・10 assertions で確認。JSON エラー応答は変更しない。

## 2026-11-03: 管理画面の NodeTemplate 移行

* 利用者の指示で管理・管理操作の全 20 画面を既存 URL のまま NodeTemplate に切り替えた。右上の `2` は識別用に維持する。業務 Logic・ルート・認可／CSRF・Ajax・ダウンロードは変更しない。旧 Smarty ソースは未参照の比較資料として残す。
* 管理の統合 49 件を追加し、既存画面と合わせて 166 件・1051 assertions が成功。属性・ルートの単体 17 件・2150 assertions も成功。対象 PHPStan はコード指摘なし、既存の未使用 ignoreErrors 3 件で終了コード 1。モックを使う範囲は [テンプレート移植](./TEMPLATE-MIGRATION.md) を参照。
* 実ブラウザの `/management` は管理者権限のないセッションで 403（開発用 Whoops の画面）だったため、管理画面の実ブラウザ操作は未確認。実メール送信・実環境の保守操作は行っていない。

## 2026-11-03: 移植済み画面を正式 URL へ一本化

* 利用者の承認によりトップ・account・password・plugin を正式 URL の NodeTemplate 版へ統一した。さらに確認フォームで「2付きURL・アクション名・右上の2・開発用リンクをすべて撤去」を選択されたため、互換経路も残していない。送信済みの `2` 付きメールリンクは利用できない。
* 通常名のコントローラアクションで Node ページを描画し、リンク・フォーム・ページャー・DL・成功遷移・登録／再設定メール内 URL も `/`、`/account/...`、`/password/...`、`/plugin/...` に統一した。未移植画面・共有 Ajax/API・メール本文の Smarty は維持する。以下の並行移植記録は履歴であり、現在の経路方針ではない。
* 初回ログインで一度発生した匿名セッション前提条件のエラーは原因未確定。今回の経路整理で解消したとは扱わない。
* 単体 17 件・2358 assertions、統合 117 件・776 assertions が成功。廃止した経路への GET・HEAD・POST が一致しないことも検証した。ブラウザでは正式経路のトップ・アカウント・編集・プラグイン一覧／詳細と Markdown プレビュー・ID 自動生成、識別表示の撤去を確認した。メール送信・入力内容の保存は行っていない。対象 PHPStan は既存の未使用 config プロパティ 1 件と未使用 ignoreErrors 3 件で終了コード 1。config は変更前から未使用であることを確認済み。

## 2026-11-03: 移植済み画面の 2 系統をルートグループへ統一（過去）

* 新側を `/index2`、`/account2/...`、`/password2/...`、`/plugin2/...` に統一した。入口だけでなく配下も既存の末尾 2 アクションへ接続し、リンク・フォーム・ページャー・DL・成功時遷移・登録／再設定メール内 URL を揃えた。経路一覧は [テンプレート移植](./TEMPLATE-MIGRATION.md) を参照する。
* 以前の `/account/user2`、`/password/reset2/{token}`、`/plugin/detail2/{plugin_id}` 等は互換経路として残した。旧 `/plugin` と `/plugin/{plugin_id}` は Smarty のまま。account/password の旧 URL を Node へ委譲する過去の明示指示は維持し、新規の切り替えはしていない。未移植画面・Ajax/API・メール本文の Smarty は変更していない。
* 単体 16 件・1059 assertions、統合 206 件・1469 assertions が成功。新ルート・旧ルート・互換経路、画面、POST、メール、完了遷移を検証した。認証・CSRF・所有者フィルター等はルート単体で登録構成を確認し、ミドルウェアを省く統合基盤での実効性検証とは区別する。
* 対象 PHPStan はコード指摘なし。既存の未使用 ignoreErrors 3 件のみで終了コード 1。ブラウザ・実メール送信は未確認。

## 2026-11-03: account・password の旧 URL も NodeTemplate へ切り替え

* 利用者からパスワード変更後に旧 user へ戻ると報告があり、確認のうえ「旧URLも含めて、account/*・password/* をすべてNodeTemplate版にする」と明示された。[AccountController](../PeServer/App/Controllers/Page/AccountController.php) と [PasswordController](../PeServer/App/Controllers/Page/PasswordController.php) の旧 Web アクションを対応する末尾 2 のアクションへ委譲した。以下の過去記録にある旧 Smarty 経路維持は、この切り替えで更新された。
* 既存 URL と 2 付き URL はどちらも利用できる。旧 URL で直接 POST しても、失敗再表示は NodeTemplate、フォーム・成功遷移・メール内リンクは 2 系統になる。重複する旧描画処理とプラグイン用 private helper は除去した。Smarty の Web テンプレート自体は削除せず、メールは引き続き Smarty を使う。
* パスワード変更・編集・メール確定後は user2、登録完了・ログアウト・パスワード再設定後は index2 へ遷移する。セットアップ・管理への遷移、業務 Logic とルート・ミドルウェア構成は変更していない。
* 統合 143 件・1005 assertions、ルート単体 4 件・454 assertions が成功した。旧 URL を含むパスワード変更後の user2 描画、各画面の識別表示、フォーム・成功遷移・メール内リンクを確認した。対象コントローラの PHPStan はエラーなし。実ブラウザ確認は未実施。ミドルウェアの実効性は統合基盤では検証していない。

## 2026-11-03: パスワード再発行の並行移植

* 開始・メール送信案内・再設定の 3 画面を [パスワードページ群](../PeServer/App/Views/Pages/Password/) に移植し、[PasswordController](../PeServer/App/Controllers/Page/PasswordController.php) から接続した。`/password/reminder2`、`/password/reminding2/{token}`、`/password/reset2/{token}` を追加し、旧経路・業務処理・成功後のトップ遷移は維持した。
* [AccountLoginPage](../PeServer/App/Views/Pages/Account/AccountLoginPage.php) の再発行リンクを reminder2 へ変更した。PasswordReminderLogic に既定 `password/reset` の resetPath を追加し、新 POST だけ `password/reset2` を渡す。メール本文は Smarty のまま、リンクだけ新系統に揃える。
* メール依存は既存の Mailer → AppMailer マッピングへ合わせ、統合テストで Mailer を差し替えた。外部メール送信は行わず、Smarty の本文生成と送信先・再設定 URL を検証した。
* 単体 8 件・531 assertions、統合 20 件・160 assertions が成功した。新旧の画面、フォーム・CSRF hidden、入力エラー、ログイン ID のエスケープと再表示、パスワード非復元、匿名セッションの前提条件、未知のログイン ID に対する既存通知処理、メール URL からの再設定完了・パスワードハッシュ・トークン消去・監査を確認した。ルートのメソッド・トークンパラメーター・NotLogin／CSRF の登録構成は単体テストで確認した。統合基盤はミドルウェアを省略するため、認証・CSRF の実効性確認とは扱わない。実ブラウザ・実メール配信は未確認。
* 対象 PHPStan は変更コードへの指摘なし。既存の未使用 ignoreErrors 3 件だけで終了コードが失敗となる。無関係な設定・既存業務処理は修正していない。

## 2026-11-03: アカウント全画面の並行移植

* 残るユーザー情報、APIキー、パスワード、メール変更、プラグイン登録・編集・予約、監査ログを [アカウントページ群](../PeServer/App/Views/Pages/Account/) に追加した。[AccountController](../PeServer/App/Controllers/Page/AccountController.php) と [ルート設定](../PeServer/App/Models/AppRouteSetting.php) に末尾 2 の経路・アクションを追加し、旧経路は維持した。入口は `/account/index2`、ログイン済みユーザー情報は `/account/user2`。ログアウトと監査ログ DL も 2 の別経路から既存処理へ委譲する。
* 新画面のフォーム・操作リンク・共通ヘッダーメニュー・成功遷移を 2 系統に揃えた。既存の login2・edit2 の成功遷移も新系統へ更新した。当初追加した accountFamily2 フラグは、利用者の「NodeTemplate を使う処理では 2 に寄せていい」という方針により撤去し、NodeTemplate の共通ヘッダーは常に 2 系統へ向ける。Smarty 側とセットアップ・管理・トップページへの既存遷移は変更しない。
* Core に [Pager](../PeServer/Core/Mvc/Template/Node/Html/Components/Pager/Pager.php) と [Code](../PeServer/Core/Mvc/Template/Node/Html/Components/Code/Code.php) を追加した。[MarkdownEditor](../PeServer/Core/Mvc/Template/Node/Html/Components/MarkdownEditor/MarkdownEditor.php) でユーザー編集とプラグイン編集の同一タブを共有する。InputHelper の id 上書き・readOnly を追加し、name・値・エラーの参照キーは従来どおり維持した。
* 成功後のフラッシュメッセージで数値キーが DataAttributes.build に int として渡るエラーを検出した。[DataAttributes](../PeServer/Core/Mvc/Template/Node/Html/Attribute/DataAttributes.php) の getNames がキーを文字列として列挙するように修正した。DefaultPage の `[$key => $value]` 指定は変更せず、数値文字列・負数のキーによる属性生成を回帰テストした。
* 旧 [user_api.tpl](../PeServer/App/Views/Account/user_api.tpl) の td に対する誤った th 閉じタグは、新ノード構造で正しく閉じる。メール本文・メールレイアウトは Smarty のまま残した。
* 単体 65 件・746 assertions、新旧のログイン・signup・アカウント統合 107 件・688 assertions が成功した。新画面の描画、CSRF hidden の出力、全ルートのメソッド・パラメーター・ミドルウェア登録、入力エラー再表示と値復元、APIキー作成・削除とシークレットの一度だけ表示、パスワード更新、メール変更要求・不正トークン・確認、プラグイン登録・編集・予約とカテゴリー保存、Markdown プレビュー、監査ログのページング・DL を確認した。最終のメールテストでは Mailer をモックし、本文の Smarty 生成は実行した。実ブラウザ・実メール配信は未確認。統合基盤ではミドルウェアを省略するため、認証・CSRF の実効性を検証したとの扱いにはしない。
* 対象 PHPStan の変更コードへの診断はない。既存 ignoreErrors の未使用 3 件だけで終了コードは失敗となる。無関係な設定の変更・抑制追加は行っていない。差分の空白検査も成功した。

## 2026-11-03: ユーザー登録の並行移植

利用者が新規登録フローの実動を確認済み（「おー 動いた」）。以下の実装時点の記録にある実ブラウザ未確認は、その後の確認で更新された。

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

利用者が編集画面の軽い実動確認済み。後続のアカウント全画面移植では、成功先を user2 に変更した。

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
