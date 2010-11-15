{capture assign=test}
    outerstart
        {capture assign=test2}
            inner
        {/capture}
    outerend
{/capture}

[ {$test} {$test2} ]