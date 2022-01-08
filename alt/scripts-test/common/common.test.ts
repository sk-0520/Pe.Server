import * as common from '../../scripts/common/common';

describe('common', () => {
	test('joinPath', () => {
		expect(common.joinPath('', '')).toBe('/');
		expect(common.joinPath('/', '/')).toBe('/');
		expect(common.joinPath('/a/b', '/c/', 'd/', '/e')).toBe('/a/b/c/d/e');
		expect(common.joinPath('/a/b/', '/c/', 'd/', '/e')).toBe('/a/b/c/d/e');
		expect(common.joinPath('/a///////', 'b')).toBe('/a/b');
		expect(common.joinPath('a', '/////b', 'c/////////', '///////d////////')).toBe('a/b/c/d');
	});

	test('toBoolean', () => {
		expect(common.toBoolean(null)).toBe(false);
		expect(common.toBoolean(undefined)).toBe(false);
		expect(common.toBoolean("")).toBe(false);
		expect(common.toBoolean("t")).toBe(false);
		expect(common.toBoolean("f")).toBe(false);
		expect(common.toBoolean("false")).toBe(false);
		expect(common.toBoolean("FALSE")).toBe(false);
		expect(common.toBoolean("true")).toBe(true);
		expect(common.toBoolean("TRUE")).toBe(true);
	});

	test('toString', () => {
		expect(common.toString(null)).toBe('false');
		expect(common.toString(undefined)).toBe('false');
		expect(common.toString(false)).toBe('false');
		expect(common.toString(true)).toBe('true');
	});

	test('padding', () => {
		expect(common.padding(0, 2, ' ')).toBe(' 0');
		expect(common.padding(1, 2, ' ')).toBe(' 1');
		expect(common.padding(10, 2, ' ')).toBe('10');
		expect(common.padding(100, 2, ' ')).toBe('100');
		expect(common.padding(100, 4, ' ')).toBe(' 100');
		expect(common.padding(100, 4, '0')).toBe('0100');
		expect(common.padding(10, 4, '0')).toBe('0010');
		expect(common.padding(1, 4, '0')).toBe('0001');
		expect(() => common.padding(-1, 10, '0')).toThrow();
		expect(() => common.padding(1, 10, '00')).toThrow();
	});

});
