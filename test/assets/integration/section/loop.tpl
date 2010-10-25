{section name=foo start=10 loop=20 step=2}
  * {$smarty.section.foo.index}
{/section}
---
{section name=bar loop=21 max=6 step=-2}
  * {$smarty.section.bar.index}
{/section}