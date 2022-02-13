# API

グッダグダAPI。

## 共通

* 基本となるパスは `/api/` となる
* 要求本文は全て JSON で与える
  * APIキーが必要なものはヘッダにキーを指定する
    * キー名: `x-pe-api-key`
    * 例: `x-pe-api-key: xxxxxxxxxxxxxxxxxx`
* 応答本文は全て JSON で返す
  * 応答成功時はステータスコード `200`

## API一覧

### プラグイン

#### 存在確認

| 項目 | 内容 |
|---|---|
| エンドポイント | `/api/plugin/exists` |
| メソッド | POST |
| 認証 | 不要 |
| 本文 | 必要 |

##### パラメータ

`plugin_id`
:   型: `GUID(string)`
:   プラグインID

`plugin_name`
:   型: `string`
:   プラグイン内部名
