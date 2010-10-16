{foreach from=$foos item=foo name=outer}
    {$smarty.foreach.outer.iteration} of {$smarty.foreach.outer.total}

    {foreach from=$foo item=f name=inner}
        {$smarty.foreach.outer.iteration} of {$smarty.foreach.outer.total}
        {$smarty.foreach.inner.iteration} of {$smarty.foreach.inner.total}  
    {/foreach}
    
    {$smarty.foreach.outer.iteration} of {$smarty.foreach.outer.total}
{/foreach}