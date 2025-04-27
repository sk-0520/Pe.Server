import * as default_loader from "../default_loader";
import * as management_database_list from "../domain/management/database_list";

window.addEventListener("DOMContentLoaded", (ev) => {
	default_loader.boot();
	management_database_list.boot();
});
