{extends file='default.tpl'}
{block name='TITLE'}API{/block}
{block name='BODY'}

{markdown level=constant('PeServer\\App\\Models\\Domain\\UserLevel::ADMINISTRATOR') class='api'}{$values.api_document}{/markdown}

{/block}

