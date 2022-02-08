import * as navigator from './domain/navigator';
import * as clipboard_copy from './domain/clipboard_copy';

export function boot() {
	navigator.boot();
	clipboard_copy.boot();
}
