import * as default_loader from "../default_loader";
import * as management_feedback_list from "../domain/management/feedback_list";

window.addEventListener("DOMContentLoaded", (ev) => {
	default_loader.boot();
	management_feedback_list.boot();
});
