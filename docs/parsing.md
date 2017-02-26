Parsing
=======
> Information about parsing

This document contains some comments regarding parsing

This setup was inspired by the symfony yaml component but took a turn in to favouring regex for parsing.

# strings

* each string newline
* quoted "" = string
* slashes // = regex
* {} = hex


# Lexing

Since yara's format is pretty broad, lexing is a better approach. Would be 

*   https://github.com/nikic/Phlexy

*    $binNumberRegex = `'0b[01]+';`
*    $hexNumberRegex = `'0x[0-9a-f]+';`
*    $decNumberRegex = `'0|[1-9][0-9]*';`
*    $octNumberRegex = `'0[0-9]+'; // 0-9 is intentional`

# Regex strings

* Escaping `(\\x00|\\n|\\r|\\|'|"|\\x1a)`