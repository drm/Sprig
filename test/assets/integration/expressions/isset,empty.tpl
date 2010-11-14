{if isset($foo)}foo is set{else}foo is not set{/if}

{if empty($bar)}bar is empty{else}bar is not empty{/if}

{if isset($foos.bar1)}foos.bar1 is set{/if}

{if empty($foos.bar4)}foos.bar4 is empty{/if}

{if !isset($foo)}foo is not set{else}foo is set{/if}

{if !empty($bar)}bar is not empty{else}bar is empty{/if}

{if !isset($foos.bar1)}foos.bar1 is not set{/if}

{if !empty($foos.bar4)}foos.bar4 is not empty{/if}


