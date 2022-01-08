
/**
 * サイトの最上位パス(/)を取得する。
 *
 * 通常使用では / を直接使用すればよいが、
 * appsettings<*>.json の PublicUrl による指定でリバースプロキシなどで最上位パスが /xxx となるためこの関数で最上位パスを取得すること
 */
export function getBaseUrl(): string {
	const targetMetaElement = <HTMLMetaElement>document.head.querySelector("[name=pg-base-url]");
	console.debug(targetMetaElement?.outerHTML);
	if (!targetMetaElement) {
		return '/';
	}

	console.debug(targetMetaElement.content);

	return targetMetaElement.content;
}

/**
 * パス文字列の結合
 * @param base 基点となるパス
 * @param paths 結合するパス
 */
export function joinPath(base: string, ...paths: string[]): string {
	while (base.endsWith('/')) {
		base = base.substr(0, base.length - 1);
	}

	function chomp(s: string): string {
		return s
			.split('/')
			.filter(i => i.length)
			.join('/')
			;
	}

	console.debug(base);
	return base + '/' + paths.map(i => chomp(i)).join('/');
}

/**
 * ベースURLからのパス結合
 * 基本的にこれだけ使っておけばOK
 * @param paths 結合するパス
 */
export function buildPath(...paths: string[]): string {
	return joinPath(getBaseUrl(), ...paths);
}

/**
 * ログインページへ強制遷移させる処理
 */
export function jumpToLogin() {
	//TODO
}

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
