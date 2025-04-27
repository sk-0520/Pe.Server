import * as regex from "./regex";
import * as string from "./string";

/**
 * ワイルドカード処理。
 *
 * `?`/`*` に全てをかけた存在。
 */
export default class Wildcard {
	//#region variable

	private _regex: RegExp;

	//#endregion

	/**
	 * 生成。
	 *
	 * @param pattern ワイルドカードパターン。
	 */
	public constructor(pattern: string) {
		// const regexPattern = regex.escape(pattern)
		// 	.replaceAll("\\?", '.')
		// 	.replaceAll("\\*", '.*')
		// 	;
		const a = regex.escape(pattern);

		const b = string.replaceAll(a, "\\?", ".");

		const c = string.replaceAll(b, "\\*", ".*");

		this._regex = new RegExp(`^${c}$`);
	}

	//#region function

	/**
	 * インスタンスを使用せずにワイルドカード判定。
	 *
	 * @param input 入力。
	 * @param pattern ワイルドカードパターン。
	 * @returns
	 */
	public static test(input: string, pattern: string): boolean {
		const wildcard = new Wildcard(pattern);
		return wildcard.match(input);
	}

	/**
	 * ワイルドカード判定。
	 *
	 * @param input 入力。
	 * @returns
	 */
	public match(input: string): boolean {
		return this._regex.test(input);
	}

	//#endregion
}
