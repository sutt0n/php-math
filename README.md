# Algebraic Parse Tree
- Math class for solving algebraic equations via binary/parse tree

## Description
Originally, I was going to put this in a gist, but I'm not going to haphazardly place this in a repository for someone to use and think, "Why has this man done this to us?"

- *What does this do?*
- This is a simple way to substitute variables into equations and solve them via [parsing expressions by recursive descent](http://www.engr.mun.ca/~theo/Misc/exp_parsing.htm).

- *How do I use it?*
```
$math = new \math(
	$expression,		// Ex: (%x + 5(%y) / 4) * %z
	$variables,		// ["x" => 5, "y" => 2, "z" => 3]
	"%"			// Variable pre-indicator
);
```
