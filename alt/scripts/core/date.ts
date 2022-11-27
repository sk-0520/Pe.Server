/*
簡易日付処理。
[注意] 規模が大きくなれば moment 等を導入する
*/
import * as number from './number';

/**
 * なんちゃって書式処理
 *
 * @param format y*, M*, d*, H*, m*, s*
 *        https://docs.microsoft.com/ja-jp/dotnet/standard/base-types/custom-date-and-time-format-strings
 * @param date
 */
export function format(format: string, date: Date) {
	const map: { [key: string]: (s: string) => string } = {
		'y': s => (date.getFullYear() - 2000).toString(), // getYaer が ts にないっていうね...
		'yy': s => number.padding(date.getFullYear() - 2000, 2, '0'),
		'yyy': s => number.padding(date.getFullYear(), 3, '0'),
		'yyyy': s => number.padding(date.getFullYear(), 4, '0'),
		'yyyyy': s => number.padding(date.getFullYear(), 5, '0'),

		'M': s => (date.getMonth() + 1).toString(),
		'MM': s => number.padding(date.getMonth() + 1, 2, '0'),

		'd': s => date.getDate().toString(),
		'dd': s => number.padding(date.getDate(), 2, '0'),

		'H': s => date.getHours().toString(),
		'HH': s => number.padding(date.getHours(), 2, '0'),

		'm': s => date.getMinutes().toString(),
		'mm': s => number.padding(date.getMinutes(), 2, '0'),

		's': s => date.getSeconds().toString(),
		'ss': s => number.padding(date.getSeconds(), 2, '0'),
	};

	return format.replace(
		/((y{1,5})|(M{1,2})|(d{1,2})|(H{1,2})|(m{1,2})|(s{1,2}))/g,
		m => {
			return map[m](m);
		}
	);
}


