{extends file='default.tpl'}
{block name='TITLE'}開発用: streaming{/block}

{block name='BODY'}

	<button id="streaming" type="button">streaming</button>
	<output id="output"></output>

{/block}

{block name='SCRIPTS'}
	<script>
		window.addEventListener('load', () => {
			const streamingElement = document.getElementById('streaming');
			const outputElement = document.getElementById('output');

			streamingElement.addEventListener('click', async () => {
				outputElement.textContent = '';
				let bytes = 0;
				var decoder = new TextDecoder("utf-8");
				const response = await fetch('/dev/streaming/ajax');
				for await (const chunk of response.body) {
					outputElement.textContent += decoder.decode(Uint8Array.from(chunk).buffer);
				}
			}, false);

		});
	</script>
{/block}
