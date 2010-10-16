{foreach from=$foo item=bar}
    {$bar}
{foreachelse}
    baz
{/foreach}

{foreach from=$other item=bar}
    {$bar}
{foreachelse}
    baz
{/foreach}