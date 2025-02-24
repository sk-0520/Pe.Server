{extends file='default.tpl'}
{block name='TITLE'}開発用: streaming{/block}

{block name='BODY'}

	<p>
		<button id="chunk_start" type="button">chunk</button>
		<output id="chunk_output"></output>
	</p>

	<p>
		<button id="sse_text_start" type="button">sse/text</button>
		<output id="sse_text_output"></output>
	</p>

	<p>
		<button id="sse_json_start" type="button">sse/json</button>
		<output id="sse_json_output"></output>
	</p>

{/block}

{block name='SCRIPTS'}
	<script>
		window.addEventListener('load', () => {
			const fireElement = document.getElementById('chunk_start');
			const outputElement = document.getElementById('chunk_output');

			fireElement.addEventListener('click', async () => {
				outputElement.textContent = '';
				let bytes = 0;
				var decoder = new TextDecoder("utf-8");
				const response = await fetch('/ajax/dev/streaming_chunk');
				for await (const chunk of response.body) {
					outputElement.textContent += decoder.decode(Uint8Array.from(chunk).buffer);
				}
			}, false);

		});

		window.addEventListener('load', () => {
			const fireElement = document.getElementById('sse_text_start');
			const outputElement = document.getElementById('sse_text_output');

			fireElement.addEventListener('click', async () => {
				outputElement.textContent = '';
				const eventSource = new EventSource("/ajax/dev/streaming_sse/text");
				eventSource.onmessage = (event) => {
					console.log(event);
					if(event.data === "<DONE>") {
						eventSource.close();
						return;
					}
					outputElement.textContent += event.data;
				};
			}, false);

		});


		window.addEventListener('load', () => {
			const fireElement = document.getElementById('sse_json_start');
			const outputElement = document.getElementById('sse_json_output');

			fireElement.addEventListener('click', async () => {
				outputElement.textContent = '';
				const eventSource = new EventSource("/ajax/dev/streaming_sse/json");
				eventSource.onmessage = (event) => {
					console.log(event);
					if(event.data === "<DONE>") {
						eventSource.close();
						return;
					}
					const value = JSON.parse(event.data)
					outputElement.textContent += value.content;
				};
			}, false);

		});
	</script>
{/block}
