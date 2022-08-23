{extends file='default.tpl'}
{block name='TITLE'}環境情報{/block}
{block name='BODY'}

{$values.phpinfo nofilter}

{/block}
