{assign var=t value="This is a string with `$foo.bar` in it"}
{$t}
{assign var=t value="This is a string with `$foos[1].bar2` in it"}
{$t}

