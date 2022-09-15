import * as default_loader from '../default_loader';
import * as feedback_list from '../domain/feedback_list'


window.addEventListener('DOMContentLoaded', ev => {
	default_loader.boot();
	feedback_list.boot();
});
