import Wildcard from '../../scripts/core/wildcard';

describe('Wildcard', () => {
	test.each([
		[true, '', ''],
		[true, '', '*'],
		[false, '', '?'],
		[true, 'abcde', '*bcd*'],
	])('test', (expected: boolean, input: string, pattern: string) => {
		expect(Wildcard.test(input, pattern)).toBe(expected);
	});
});
