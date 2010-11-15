{foreach from=$foos item=foo}
    {foreach from=$foo item=f}
        {$f}
    {/foreach}
{/foreach}