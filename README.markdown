# Sprig #
Sprig is a [Smarty](http://smarty.net/) compatibility layer for the [Twig](http://twig-project.org/) template engine.

## Goals ##
The primary goal of this project is to be a drop-in replacement for
Smarty whilst gaining all the advantages that Twig has to offer.

## Target audience ##
People who wish to use a template language in PHP project, are currently using Smarty in existing projects, but wish to move on to a better solution without the need to rewrite an entire set of templates.

## Features ##
### Compatibility features ###
- Built on top of Twig's robust engine
- Aims to support all Smarty's syntax
    - Dollar variable prefixes 
     
            {$var}            
    - In-string variable expansion 
     
            {include file="header.tpl" value="Welcome, `$user.name`"}
    - Smarty-style end-of-block notation 
     
            {if $item} ... {/if}            
- Aims to support all Smarty's native constructs:
  - `foreach`
  - `assign`
  - `include`
  - `capture`
  - `section`
- A plugin loader for:
  - Modifiers
  - Functions
  - Blocks
  - Compiler plugins (_under development_)
- Twig compatibility
  - Variable and block delimiters are configurable

### Migration features ###
- A warning-stack, warning you about Smarty native syntax (_under development_)

## Status ##
While making huge progress, the project is still under **heavy** development. Please contribute to this project by testing, providing issue reports, usage documentation and/or unit tests for features or bugs. 

Keep up to date by following the changes on the [Sprig wiki](http://github.com/drm/Sprig/wiki) and the [Issue list](http://github.com/drm/Sprig/issues)


