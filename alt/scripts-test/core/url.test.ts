import * as url from '../../scripts/core/url';

describe('url', () => {
	test.each([
		[false, ''],
		[false, ' http:// '],

		[false, 'http'],
		[false, 'http:'],
		[false, 'http:/'],
		[false, 'http://'],
		[true, 'http://x'],

		[false, 'https'],
		[false, 'https:'],
		[false, 'https:/'],
		[false, 'https://'],
		[true, 'https://x'],
	])('isHttpUrl', (expected: boolean, input: string) => {
		expect(url.isHttpUrl(input)).toBe(expected);
	});

	test('joinPath', () => {
		expect(url.joinPath('a', 'b')).toBe('a/b');
		expect(url.joinPath('a/', 'b')).toBe('a/b');
		expect(url.joinPath('a', '/b')).toBe('a/b');
		expect(url.joinPath('a/', '/b')).toBe('a/b');
		expect(url.joinPath('a//', '//b')).toBe('a/b');
		expect(url.joinPath('/a//', '//b')).toBe('/a/b');
		expect(url.joinPath('a//', '//b/')).toBe('a/b');
		expect(url.joinPath('/a//', '//b/')).toBe('/a/b');
		expect(url.joinPath('//a//', '//b//')).toBe('//a/b');

		expect(url.joinPath('', '')).toBe('/');
		expect(url.joinPath('/', '/')).toBe('/');
		expect(url.joinPath('/a/b', '/c/', 'd/', '/e')).toBe('/a/b/c/d/e');
		expect(url.joinPath('/a/b/', '/c/', 'd/', '/e')).toBe('/a/b/c/d/e');
		expect(url.joinPath('/a///////', 'b')).toBe('/a/b');
		expect(url.joinPath('a', '/////b', 'c/////////', '///////d////////')).toBe('a/b/c/d');
	})
});
