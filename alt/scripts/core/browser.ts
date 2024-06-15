export function download(name: string, content: Blob | MediaSource) {
	const linkUrl = window.URL.createObjectURL(content);
	const anchorElement = document.createElement("a");
	anchorElement.download = name;
	anchorElement.href = linkUrl;
	anchorElement.style.display = "none";
	anchorElement.click();
	anchorElement.remove();
}
