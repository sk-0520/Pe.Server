
function isSchemeUrl(url: string, protocols: ReadonlyArray<string>): boolean {
	const starts = protocols.map(i => i + '://');

	for (const start of starts) {
		if (url.startsWith(start) && start.length < url.length) {
			return true;
		}
	}

	return false;
}

export function isHttpUrl(s: string): boolean {
	return isSchemeUrl(s, [
		'https',
		'http',
	]);
}

/**
 * パス文字列の結合。
 *
 * @param base 基点となるパス
 * @param path1 結合するパス
 * @param pathN 結合するパス
 */
export function joinPath(base: string, path1: string, ...pathN: ReadonlyArray<string>): string {
	while (base.endsWith('/')) {
		base = base.substring(0, base.length - 1);
	}

	function chomp(s: string): string {
		return s
			.split('/')
			.filter(i => i)
			.join('/')
			;
	}

	const paths = [path1];
	paths.push(...pathN);

	//console.debug(base);
	return base + '/' + paths.map(i => chomp(i)).join('/');
}
