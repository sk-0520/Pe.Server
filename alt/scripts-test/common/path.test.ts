import * as path from '../../scripts/common/path';

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
	})
});
