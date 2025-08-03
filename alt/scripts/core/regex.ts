const escapeRegex = /[.*+?^${}()|[\]\\]/g;

/**
 * 正規表現エスケープ。
 *
 * @param source
 * @returns
 */
// biome-ignore lint/suspicious/noShadowRestrictedNames: 名前空間とは何だったのか
export function escape(source: string): string {
	return source.replace(escapeRegex, "\\$&");
}
