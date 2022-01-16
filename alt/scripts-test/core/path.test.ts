import * as path from '../../scripts/core/path';

describe('path', () => {
	test('join', () =>{
		expect(path.join('a', 'b')).toBe('a/b');
		expect(path.join('a/', 'b')).toBe('a/b');
		expect(path.join('a', '/b')).toBe('a/b');
		expect(path.join('a/', '/b')).toBe('a/b');
		expect(path.join('a//', '//b')).toBe('a/b');
		expect(path.join('/a//', '//b')).toBe('/a/b');
		expect(path.join('a//', '//b/')).toBe('a/b');
		expect(path.join('/a//', '//b/')).toBe('/a/b');
		expect(path.join('//a//', '//b//')).toBe('//a/b');

		expect(path.join('', '')).toBe('/');
		expect(path.join('/', '/')).toBe('/');
		expect(path.join('/a/b', '/c/', 'd/', '/e')).toBe('/a/b/c/d/e');
		expect(path.join('/a/b/', '/c/', 'd/', '/e')).toBe('/a/b/c/d/e');
		expect(path.join('/a///////', 'b')).toBe('/a/b');
		expect(path.join('a', '/////b', 'c/////////', '///////d////////')).toBe('a/b/c/d');
	})
});
