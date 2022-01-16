import * as dom from '../../scripts/core/dom';

describe('dom', () => {
	test('toCustomKey', () =>{
		expect(dom.toCustomKey('')).toBe('');
		expect(dom.toCustomKey('a')).toBe('a');
		expect(dom.toCustomKey('aB')).toBe('ab');
		expect(dom.toCustomKey('aBc')).toBe('abc');
		expect(dom.toCustomKey('a-b')).toBe('aB');
		expect(dom.toCustomKey('a-b-c')).toBe('aBC');
		expect(dom.toCustomKey('dataset-key-data')).toBe('datasetKeyData');
	})
});
