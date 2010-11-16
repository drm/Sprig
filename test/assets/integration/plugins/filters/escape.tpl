{assign var=url value="<http://www.google.com/>"}

{$url|escape}
{$url|escape:'url'}
{$url|escape:'hex'}
{$url|escape:'hexentity'}