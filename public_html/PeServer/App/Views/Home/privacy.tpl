{extends file='default.tpl'}
{block name='TITLE'}プライバシーポリシー{/block}
{block name='BODY'}

	{markdown level=constant('PeServer\\App\\Models\\Domain\\UserLevel::ADMINISTRATOR') class='privacy'}{$values.privacy_policy nofilter}{/markdown}

{/block}
