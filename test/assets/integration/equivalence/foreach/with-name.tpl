{foreach from=$foos item=foo name=magic}
    iteration {$smarty.foreach.magic.iteration} of {$smarty.foreach.magic.total}  
{/foreach}