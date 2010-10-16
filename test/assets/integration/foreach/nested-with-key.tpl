{foreach from=$foos item=foo key=outerKey}
    {$outerKey}

    {foreach from=$foo item=f key=innerKey}
        {$innerKey} 
    {/foreach}

    {$outerKey}
{/foreach}