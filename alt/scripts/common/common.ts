
/**
 * 非同期待機
 *
 * @param msec 停止時間(ミリ秒)
 */
export function sleepAsync(msec: number): Promise<void> {
	return new Promise((resolve, _) => {
		setTimeout(() => {
			resolve()
		}, msec);
	});
}

export function toBoolean(s: string | null | undefined): boolean {
	if (!s) {
		return false;
	}

	return s.toLowerCase() == 'true';
}

export function toString(b: boolean | null | undefined): string {
	if (b) {
		return 'true'
	}

	return 'false';
}

export function escapeRegex(source: string) {
	return source.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}


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
