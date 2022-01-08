/**
 * パス文字列の結合
 * @param base 基点となるパス
 * @param paths 結合するパス
 */
export function join(base: string, ...paths: string[]): string {
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

	//console.debug(base);
	return base + '/' + paths.map(i => chomp(i)).join('/');
}
