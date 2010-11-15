{capture assign="html"}
    <h1>Lorem ipsum</h1>
    <p class="intro">dolor sit amet</p>
    <p>consectetuer adipiscing elit</P>
{/capture}

[{$html|strip_tags}]
[{$html|strip_tags:true}]