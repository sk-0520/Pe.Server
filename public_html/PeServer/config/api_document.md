# API

グッダグダAPI。

## 共通

* 基本となるパスは `/api/` となる
* 要求本文は全て JSON で与える
* 応答本文は全て JSON で返す
* APIキーが必要なものはヘッダにキーを指定する
  * キー名: `x-pe-api-key`
  * 例: `x-pe-api-key: xxxxxxxxxxxxxxxxxx`

## API一覧

### プラグイン

#### 存在確認

| 項目 | 内容 |
|---|---|
| エンドポイント | `/api/plugin/exists` |
| メソッド | POST |
| 認証 | 不要 |
| 本文 | 必要 |
