function attachTableButton(tableButtonElement: HTMLButtonElement) {
	tableButtonElement.addEventListener("click", (ev) => {
		const tableButtonElement = ev.target as HTMLElement;
		const tableName = tableButtonElement.dataset.table ?? "";
		const columnsJson = tableButtonElement.dataset.columns ?? "";
		const columns = JSON.parse(columnsJson) as [];
		const sqlLines = [
			"select",
			`	${columns.map((i) => i.name).join(", ")}`,
			"from",
			`	${tableName}`,
		];
		if (columns.filter((i) => i.pk).length) {
			sqlLines.push("order by");
			sqlLines.push(
				`	${columns
					.filter((i) => i.pk)
					.map((i) => i.name)
					.join(", ")}`,
			);
		}

		const textAreaElement = document.getElementById(
			"database_maintenance_statement",
		) as HTMLTextAreaElement;
		textAreaElement.textContent = sqlLines.join("\r\n");
	});
}

export function boot() {
	const buttonList =
		document.querySelectorAll<HTMLButtonElement>("#tables .pg-table");
	for (const buttonElement of buttonList) {
		attachTableButton(buttonElement);
	}
}
