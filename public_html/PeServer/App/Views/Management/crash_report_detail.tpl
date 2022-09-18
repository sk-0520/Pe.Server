{extends file='default.tpl'}
{block name='TITLE'}クラッシュレポート: {*{$values.detail->sequence}*}{/block}
{block name='BODY'}

	<dl>
		<dt>シーケンス</dt>
		{* <dd>{$values.detail->sequence}</dd> *}
	</dl>

{/block}
