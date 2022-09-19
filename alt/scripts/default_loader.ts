import * as navigator from './domain/navigator';
import * as submit_link from './domain/submit_link';
import * as clipboard_copy from './domain/clipboard_copy';

export function boot() {
	navigator.boot();
	submit_link.boot();
	clipboard_copy.boot();
}
