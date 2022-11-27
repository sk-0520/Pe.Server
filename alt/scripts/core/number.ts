

/**
 * 数値埋め処理。
 *
 * なぜこれを作ったのか、謎い。
 *
 * @param input 数値。
 * @param width 幅数。
 * @param c 埋め文字。
 * @returns
 */
 export function padding(input: number, width: number, c: string): string {
	if (input < 0) {
		throw new Error('input is negative');
	}
	if (c.length != 1) {
		throw new Error('c.length is ' + c.length);
	}

	const numberValue = input.toString();

	// 埋める余地がない場合はそのまま返す
	if (width <= numberValue.length) {
		return numberValue;
	}
	const count = width - numberValue.length;
	const result = c.repeat(count) + numberValue;
	return result;
}
