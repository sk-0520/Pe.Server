import * as number from '../../scripts/core/number';

describe('number', () => {
	test('padding', () => {
		expect(number.padding(0, 2, ' ')).toBe(' 0');
		expect(number.padding(1, 2, ' ')).toBe(' 1');
		expect(number.padding(10, 2, ' ')).toBe('10');
		expect(number.padding(100, 2, ' ')).toBe('100');
		expect(number.padding(100, 4, ' ')).toBe(' 100');
		expect(number.padding(100, 4, '0')).toBe('0100');
		expect(number.padding(10, 4, '0')).toBe('0010');
		expect(number.padding(1, 4, '0')).toBe('0001');
		expect(() => number.padding(-1, 10, '0')).toThrow();
		expect(() => number.padding(1, 10, '00')).toThrow();
	});
});
