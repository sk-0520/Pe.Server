import * as default_loader from '../default_loader';
import * as markdown from '../domain/markdown';

window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	markdown.boot('.markdown-editor');
});
