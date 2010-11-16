{assign var=var value=""}

{$var|default:"something default"}

{assign var=var value="something not default"}

{$var|default:"something default"}
