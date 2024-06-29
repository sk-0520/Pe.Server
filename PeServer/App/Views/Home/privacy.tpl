{extends file='default.tpl'}
{block name='TITLE'}プライバシーポリシー{/block}
{block name='BODY'}

	{markdown level='administrator' class='privacy'}{$values.privacy_policy nofilter}{/markdown}

{/block}
