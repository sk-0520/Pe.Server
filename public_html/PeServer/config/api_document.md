# API

グッダグダAPI。

## 共通

* 基本となるパスは `/api/` となる
* 要求は明記されない場合 JSON で与える
  * APIキーが必要なものはヘッダにキーを指定する
    * キー名: `x-pe-api-key`
    * 例: `x-pe-api-key: xxxxxxxxxxxxxxxxxx`
  * クエリパラメータは極力避ける
    * Web画面から使用するようなものはクエリも考慮する
* 応答本文は明記されない場合 JSON で返す
  * 応答成功時はステータスコード `200`

### JSON応答

```json
{
  "data": "object|array<object>",
  "error"?: {
    "message": "string",
    "code": "number",
    "info": "object"
  }
}
```

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

##### 応答

`plugin_id`
:   型: `boolean`
:   存在する場合に真

`plugin_name`
:   型: `boolean`
:   存在する場合に真

#### プラグイン情報

| 項目 | 内容 |
|---|---|
| エンドポイント | `/api/plugin/information` |
| メソッド | POST |
| 認証 | 不要 |
| 本文 | 必要 |

##### パラメータ

`plugin_ids`
:   型: `string[]`
:   プラグインID

##### 応答

`items`
:   型: `[key: string]: object`
:   プラグイン情報

```json
"items": {
  "プラグインID": {
    "user_id": "ユーザーID",
    "plugin_name": "プラグイン名",
    "display_name": "プラグイン表示名",
    "state": "enabled(有効)/disabled(無効)/check_failed(バージョンチェックURL無効)",
    "description": "説明",
    "check_url": "バージョンチェックURL",
    "project_url": "プロジェクトURL"
  }
}
```
