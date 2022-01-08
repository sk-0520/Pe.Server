
/**
 * AJAX 処理エラー時の返却データ構造
 */
 export interface AjaxError {
	/** 表示可能エラーメッセージ */
	error: string;
	/** 内部使用エラー内容データ */
	data: string;
	/** 対象ごとのエラー詳細 */
	info: any;
}

/**
 * AJAX 処理結果。
 */
export interface AjaxResult<T> {
	/** 正常処理結果 */
	result: T;
	/** 以上処理結果 */
	error?: AjaxError;
}


/**
 * AJAX 処理異常結果の内容は「ログイン必須」か。
 * @param error
 */
export function isNeedLogin(error: AjaxError): boolean {
	return error && error.data == 'NEED-LOGIN';
}

