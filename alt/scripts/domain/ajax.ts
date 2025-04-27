//import * as dom from './dom';

const CsrfTokenHeader: string = "X-CSRF-TOKEN";
const CsrfTokenId: string = "core__csrf_id";

/**
 * AJAX 処理エラー時の返却データ構造
 */
export interface AjaxError {
	/** 表示可能エラーメッセージ */
	message: string;
	/** 内部使用エラー内容データ */
	code: string;
	/** 対象ごとのエラー詳細 */
	info: unknown;
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

class AjaxResultImpl<T> implements AjaxResult<T> {
	readonly data: T;
	readonly error?: AjaxError;

	public constructor(obj: unknown) {
		const value = obj as AjaxResult<T>;
		this.data = value.data;
		this.error = value.error;
	}

	public toString(): string {
		return JSON.stringify(this, null, 2);
	}
}

export function toResult<T>(obj: unknown): AjaxResult<T> {
	return new AjaxResultImpl<T>(obj);
}

export async function communicateJsonAsync<T>(
	url: string,
	method: "GET" | "POST" | "PUT" | "PATCH" | "DELETE",
	json?: object,
): Promise<AjaxResult<T>> {
	const headers: { [name: string]: string } = {
		"Content-Type": "application/json",
	};
	const csrfToken = getCsrfToken();
	if (csrfToken?.length) {
		headers[CsrfTokenHeader] = csrfToken;
	}

	const data: RequestInit = {
		method: method,
		mode: "cors",
		cache: "no-cache",
		credentials: "same-origin",
		headers: headers,
		redirect: "follow",
	};
	if (json) {
		data.body = JSON.stringify(json);
	}

	const response = await fetch(url, data);

	const result = await response.json();
	return toResult<T>(result);
}

export function getCsrfToken(): string {
	const metaElement = document.getElementById(
		CsrfTokenId,
	) as HTMLMetaElement | null;
	return metaElement?.content ?? "";
}
