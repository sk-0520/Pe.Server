{extends file='default.tpl'}
{block name='TITLE'}プライバシーポリシー{/block}
{block name='BODY'}

{markdown level=constant('PeServer\\App\\Models\\Domains\\UserLevel::ADMINISTRATOR') class='privacy'}{$values.privacy_policy}{/markdown}

{/block}

