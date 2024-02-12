{extends file='default.tpl'}
{block name='TITLE'}メール送信{/block}
{block name='BODY'}

	<form action="/management/mail-send" method="post" enctype="multipart/form-data">
		{csrf}

		<dl class="input">
			<dt>件名</dt>
			<dd>
				{input_helper key='mail_subject' type="text" class="edit" required="true"}
			</dd>

			<dt>宛先</dt>
			<dd>
				{input_helper key='mail_to' type="email" class="edit" required="true"}
			</dd>

			<dt>本文</dt>
			<dd>
				{input_helper key='mail_body' type="textarea" class="edit" required="true"}
			</dd>

			<dt>添付ファイル</dt>
			<dd>
				{input_helper key='mail_attachment' type="file" class="edit"}
			</dd>

			<dt class="action">実行</dt>
			<dd class="action">
				<button>submit</button>
			</dd>
		</dl>
	</form>


{/block}
