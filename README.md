Yar Component
==============
> An experimental yara file parser for php compatibility

This repository contains an experimental parser for yara's .yar format.
It's meant to support at least the magento-malware-repository and tries to support as much as possible from the yara spec.
Complimentary PHP based scanners are available.

__note:__ unstable / unfinished

## Example

a default .yar example to parse

```
rule silent_banker : banker
{
    meta:
        description = "This is just an example"
        thread_level = 3
        in_the_wild = true

    strings:
        $a = {6A 40 68 00 30 00 00 6A 14 8D 91}
        $b = {8D 4D B0 2B C1 83 C0 27 99 6A 4E 59 F7 F9}
        $c = "UVODFRYSIHLNWPEJXQZAKCBGMT"

    condition:
        $a or $b or $c
}
```

### output in Json

```json
[
  {
    "name": "silent_banker",
    "tags": {
      "1": "banker"
    },
    "meta": {
      "description": "This is just an example",
      "thread_level": "3",
      "in_the_wild": "true"
    },
    "strings": [
      {
        "value": "{6A 40 68 00 30 00 00 6A 14 8D 91}",
        "name": "a",
        "type": "hex",
        "tags": null
      },
      {
        "value": "{8D 4D B0 2B C1 83 C0 27 99 6A 4E 59 F7 F9}",
        "name": "b",
        "type": "hex",
        "tags": null
      },
      {
        "value": "UVODFRYSIHLNWPEJXQZAKCBGMT",
        "name": "c",
        "type": "string",
        "tags": null
      }
    ],
    "conditions": [
      "$a or $b or $c"
    ]
  }
]
```

### Yaml

_note:_ Somehow Symfony\yaml dumps it like this, it could be formatted way better


```yaml
name: silent_banker
tags: { 1: banker }
meta: { description: 'This is just an example', thread_level: '3', in_the_wild: 'true' }
strings: [{ value: '{6A 40 68 00 30 00 00 6A 14 8D 91}', name: a, type: hex, tags: null }, { value: '{8D 4D B0 2B C1 83 C0 27 99 6A 4E 59 F7 F9}', name: b, type: hex, tags: null }, { value: UVODFRYSIHLNWPEJXQZAKCBGMT, name: c, type: string, tags: null }]
conditions: ['$a or $b or $c']
```

### XML

```xml
<?xml version="1.0"?>
<response>
    <item key="0">
        <name>silent_banker</name>
        <tags>banker</tags>
        <meta>
            <description>This is just an example</description>
            <thread_level>3</thread_level>
            <in_the_wild>true</in_the_wild>
        </meta>
        <strings>
            <value>{6A 40 68 00 30 00 00 6A 14 8D 91}</value>
            <name>a</name>
            <type>hex</type>
            <tags/>
        </strings>
        <strings>
            <value>{8D 4D B0 2B C1 83 C0 27 99 6A 4E 59 F7 F9}</value>
            <name>b</name>
            <type>hex</type>
            <tags/>
        </strings>
        <strings>
            <value>UVODFRYSIHLNWPEJXQZAKCBGMT</value>
            <name>c</name>
            <type>string</type>
            <tags/>
        </strings>
        <conditions>$a or $b or $c</conditions>
    </item>
</response>

```

### Author

This library is part of a collection of Magento security solutions created by Fabio Ros (FROSIT).

### License

This software is licensed under the [AGPL-v3.0 License](http://www.gnu.org/licenses/agpl-3.0.html)
