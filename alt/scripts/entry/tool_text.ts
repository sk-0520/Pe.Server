import * as default_loader from '../default_loader';
import * as text from '../domain/text';

window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	text.boot();
});
