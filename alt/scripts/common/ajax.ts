
/**
 * AJAX 処理エラー時の返却データ構造
 */
 export interface AjaxError {
	/** 表示可能エラーメッセージ */
	message: string;
	/** 内部使用エラー内容データ */
	code: string;
	/** 対象ごとのエラー詳細 */
	info: any;
}

/**
 * AJAX 処理結果。
 */
export interface AjaxResult<T> {
	/** 正常処理結果 */
	data: T;
	/** 以上処理結果 */
	error?: AjaxError;
}




