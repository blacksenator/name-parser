# php Name Parser

This is an enhanced version of [THE ICONIC name parser](https://github.com/theiconic/name-parser) with some adjustments mainly for german particularities.

## Purpose

Its purpose is to split a single string containing a full name, possibly including salutation, initials, suffixes etc., into meaningful parts like firstname, lastname, initials and so on.

It is mostly tailored towards german and english names but works pretty well as long as they use latin spelling.

E.g. **Otto Eduard Leopold Fürst von Bismarck-Schönhausen** is parsed to

- firstname: **Otto**
- middle names: **Eduard Leopold**
- prefix: **von**
- lastname: **Bismarck-Schönhausen**
- extension: **Fürst**

## Features

### Supported patterns

This parser is able to handle name patterns with and without comma. Without a comma, the string is first compared against **identifiers for company names** ('GmbH'., 'Co. KG' etc).
If so, parsing is skipped and the whole string will be marked as a company name.

Otherwise, the assumption is that a persons name and is in the following pattern:

```php
... [firstname] ... [lastname] ...

... [lastname] ..., ... [firstname] ...

... [lastname] ..., ... [firstname] ..., [suffix]
```

### Needles in a haystack

The strings used to search for characteristics for e.g. company names can be found in the folder `../src/Language`. I have added about a hundred string patterns, which I believe can be used to identify companies (may need to be supplemented).

### Supported parts

- salutations (e.g. Hr, Fr etc.)
- first name
- middle names
- initials (single letters, possibly followed by a dot)
- nicknames (parts within parenthesis, brackets etc.)
- last names (also supports prefixes like von, de etc.)
- suffixes (Jr, Senior etc.)
- titles (academic titles like Dr. med., Dr.h.c. etc)
- lastname extensions (nobility predicates like Gräfin, Baron)

### vCard Suppport

You can get an array whose keys match the properties for names - corresponding to [RFC 6350](https://tools.ietf.org/html/rfc6350):

So for example, **Bismarck-Schönhausen, Otto Eduard Leopold Fürst von** is converted to:

```php
[
'FN' => 'Otto Eduard Leopold Fürst von Bismarck-Schönhausen',
'N' => 'Bismarck-Schönhausen;Otto;Eduard,Leopold;;Fürst,von',
'NICKNAME' => '',
'ORG' => '',
]
```

But a company like the **Fürstlich von Bismarck'sche Brennerei GmbH** becomes:

```php
[
'FN' => "Fürstlich von Bismarck'sche Brennerei GmbH",
'N' => '',
'NICKNAME' => '',
'ORG' => "Fürstlich von Bismarck'sche Brennerei GmbH",
]
```

### Other features

- multi-language support for salutations, suffixes and lastname prefixes
- customizable nickname delimiters
- customizable normalisation of all output strings
  (original values remain accessible)
- customizable whitespace

## Examples

More than 60 different successfully parsed name patterns can be found in the [parser unit test](https://github.com/blacksenator/name-parser/blob/master/tests/ParserTest.php#L12-L12).

## Setup

```$xslt
composer require blacksenator/name-parser
```

## Usage

### Basic usage

```php
<?php

$parser = new blacksenator\NameParser\Parser();

$name = $parser->parse($input);

echo $name->getCompany();
echo $name->getSalutation();
echo $name->getFirstname();
echo $name->getLastname();
echo $name->getMiddlename();
echo $name->getNickname();
echo $name->getInitials();
echo $name->getSuffix();
echo $name->getTitle();
echo $name->getExtension();

print_r($name->getAll());

print_r($name->getVCardArray(true));

echo $name;
```

An empty string is returned for missing parts.

### Special part retrieval features

#### Explicit last name parts

You can retrieve last name prefixes and pure last names separately with

```php
echo $name->getLastnamePrefix();
echo $name->getLastname(true); // true enables strict mode for pure lastnames, only
```

#### Nick names with normalized wrapping

By default, `getNickname()` returns the pure string of nick names. However, you can pass `true` to have the same normalised parenthesis wrapping applied as in `echo $name`:

```php
echo $name->getNickname(); // Der Eiserne Kanzler
echo $name->getNickname(true); // (Der Eiserne Kanzler)
```

#### Re-print given name in the order as entered

You can re-print the parts that form a given name (that is first name, middle names and any initials) in the order they were entered in while still applying normalisation via `getGivenName()`:

```php
echo $name->getGivenName(); // Otto Eduard Leopold
```

#### Re-print full name (actual name parts only)

You can re-print the full name, that is the given name as above followed by any last name parts (excluding any salutations, nick names or suffixes) via `getFullName()`:

```php
echo $name->getFullName();
```

### Setting Languages

```php
$parser = new blacksenator\NameParser\Parser([
    new blacksenator\NameParser\Language\German(),  // default in this version
    new blacksenator\NameParser\Language\English(), // recommended
      //
])
```

### Setting nickname delimiters

```php
$parser = new blacksenator\NameParser\Parser();
$parser->setNicknameDelimiters(['(' => ')']);
```

### Setting whitespace characters

```php
$parser = new blacksenator\NameParser\Parser();
$parser->setWhitespace("\t _.");
```

### Limiting the position of salutations

```php
$parser = new blacksenator\NameParser\Parser();
$parser->setMaxSalutationIndex(2);
```

This will require salutations to appear within the first two words of the given input string. This defaults to half the amount of words in the input string, meaning that effectively the salutation may occur within
the first half of the name parts.

### Adjusting combined initials support

```php
$parser = new blacksenator\NameParser\Parser();
$parser->setMaxCombinedInitials(3);
```

Combined initials are combinations of several uppercased letters, e.g. `DJ` or `J.T.` without separating spaces. The parser will treat such sequences of uppercase letters (with optional dots) as combined initials and parse them into individual initials. This value adjusts the maximum number of uppercase letters in a single name part are recognised as comnined initials. Parts with more than the specified maximum amount of letters will not be parsed into initials and hence will most likely be parsed into first or middle names.

The default value is 2.

To disable combined initials support, set this value to 1;

## Tips

### Provide clean input strings

If your input string consists of more than just the name and directly related bits like salutations, suffixes etc., any additional parts can easily confuse the parser. It is therefore recommended to pre-process any non-clean input to isolate the name before passing it to the parser.

### Multi-pass parsing

We have not played with this, but you may be able to improve results by chaining several parses in sequence. E.g.

```php
$parser = new Parser();
$name = $parser->parse($input);
$name = $parser->parse((string) $name);
...
```

You can even compose your new input string from individual parts of
a previous pass.

### Dealing with names from different languages

The parser version is primarily built around the patterns of **german** and english names but tries to be compatible with names in other languages. Problems occur with different salutations, last name prefixes, suffixes etc. or in some cases even with the parsing order.

In order to correctly interpret a name string, it is important to know the **origin**: a German "von" as a prefix is **not** part of the surname "Bismark". On the other hand, a Dutch "van" or an Irish "Mac" is very much part of the surname.

To solve problems with salutations, last name prefixes and suffixes you can create a separate language definition file and inject it when instantiating the parser, see 'Setting Languages' above and compare the existing language files as examples.

#### Apostrophe

When adding or editing language files please consider the following: only use the **apostrophe** " ' " (U+0027)!
In names all characters [similar to apostrophes and the typographic apostroph](https://en.wikipedia.org/wiki/Apostrophe#Unicode) are mapped to U+0027 in order to eliminate this source of errors in incorrect spellings.

#### Parsing order

To deal with parsing order you may want to reformat the input string, e.g. by simply splitting it into words and reversing their order. You can even let the parser run over the original string and then over the reversed string and then pick the best results from either of the two resulting name objects. E.g. the salutation from the one and the lastname from the other.

#### Language detection

The name parser has no in-built language detection. However, you may already ask the user for their nationality in the same form. If you do that you may want to narrow the language definition files passed into the parser to the given language and maybe a fallback like english.
You can also use this information to prepare the input string as outlined above.

Alternatively, Patrick Schur as a [PHP language detection library](https://github.com/patrickschur/language-detection) that seems to deliver astonishing results. It won't give you much luck if you run it over the the name input string only, but if you have any more text from the person in their actual language, you could use this to detect the language and then proceed as above.

### Gender detection

Gender detection is outside the scope of this project.
Detecting the gender from a name often requires large lists of first name to gender mappings.

However, you can use this parser to extract salutation, first name and nick names from the input string and then use these to implement gender detection using another package (e.g. [this one](https://github.com/tuqqu/gender-detector)) or service.

### Having fun with normalisation

Writing different language files can not only be useful for parsing, but you can remap the normalised versions of salutations, prefixes and suffixes to transform them into something totally different.

E.g. you could map `Ms.` to `princess of the kingdom of` and then output the parts in appropriate order to build a pipeline that automatically transforms e.g. `Ms. Louisa Lichtenstein` into `Louisa, princess of the kingdom of Lichtenstein`.
Of course, this is a silly and rather contrived example, but you get the gist.

Of course this can also be used in more useful ways, e.g. to spell out abbreviated titles, like `Prof.` as `Professor` etc. .

## License

This fork and source THE ICONIC Name Parser library for PHP are both released under the MIT License.
