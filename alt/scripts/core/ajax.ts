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

class AjaxResultImpl<T> implements AjaxResult<T>
{
	readonly data: T;
	readonly error?: AjaxError;

	public constructor(obj: any) {
		var value = obj as AjaxResult<T>;
		this.data = value.data;
		this.error = value.error;
	}

	public toString(): string {
		return JSON.stringify(this, null, 2);
	}
}

export function toResult<T>(obj: any): AjaxResult<T> {
	return new AjaxResultImpl<T>(obj);
}


export async function communicateJsonAsync<T>(url: string, method: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE', json?: any): Promise<AjaxResult<T>> {
	const data: RequestInit = {
		method: method,
		mode: 'cors',
		cache: 'no-cache',
		credentials: 'same-origin',
		headers: {
			'Content-Type': 'application/json'
		},
		redirect: 'follow',
	};
	if (json) {
		data.body = JSON.stringify(json);
	}

	const response = await fetch(url, data);

	const result = await response.json();
	return toResult<T>(result);
}

