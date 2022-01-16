import * as date from '../../scripts/core/date';

describe('date', () => {
	test('format', () =>{
		const input = new Date(2021, 2 - 1, 8, 7, 6, 5);

		expect(date.format('y', input)).toBe('21');
		expect(date.format('yy', input)).toBe('21');
		expect(date.format('yyy', input)).toBe('2021');
		expect(date.format('yyyy', input)).toBe('2021');

		expect(date.format('M', input)).toBe('2');
		expect(date.format('MM', input)).toBe('02');

		expect(date.format('d', input)).toBe('8');
		expect(date.format('dd', input)).toBe('08');

		expect(date.format('H', input)).toBe('7');
		expect(date.format('HH', input)).toBe('07');

		expect(date.format('m', input)).toBe('6');
		expect(date.format('mm', input)).toBe('06');

		expect(date.format('s', input)).toBe('5');
		expect(date.format('ss', input)).toBe('05');

		expect(date.format('yyyyMMddHHmmss', input)).toBe('20210208070605');
		expect(date.format('yyyy-MM-ddTHH:mm:ss', input)).toBe('2021-02-08T07:06:05');

	})
});
