#TextWheel

## Overview

This is a preliminary proposal for an interoperability layer for all the different text engines that have been floating for years now around the Web.

It is *not* a proposal for a unified syntax (though it might help it happen at some point).

## Rationale

Most text engines seem keen on reinventing the wheel, each time with a different flavor and style. But they all basically do the same stuff: take a text as an input, and output formatted text according to rules that are applied in sequence.

For example while Textile does this:
```php
  @define('txt_registered', '&#174;');
  @define('txt_copyright', '&#169;');
../..
        $this->glyph = array(
../..
  'registered' => txt_registered,
  'copyright' => txt_copyright,
);
```

PHP-Typography does this:
```php
  $this->chr["copyright"] = $this->uchr(169);
  $this->chr["registeredMark"] = $this->uchr(174);
```

Again, when Drupal does "prepare paragraphs" with:
```php
  $chunk = preg_replace('|\n*$|', '', $chunk) ."\n\n"; // just to make things a little easier, pad the end
  $chunk = preg_replace('|<br />\s*<br />|', "\n\n", $chunk);
  $chunk = preg_replace('!(<'. $block .'[^>]*>)!', "\n$1", $chunk); // Space things out a little
  $chunk = preg_replace('!(</'. $block .'>)!', "$1\n\n", $chunk); // Space things out a little
  $chunk = preg_replace("/\n\n+/", "\n\n", $chunk); // take care of duplicates
  $chunk = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $chunk); // make paragraphs, including one at the end
  $chunk = preg_replace('|<p>\s*</p>\n|', '', $chunk); // under certain strange conditions it could create a P of entirely whitespace
  $chunk = preg_replace("|<p>(<li.+?)</p>|", "$1", $chunk); // problem with nested lists
  $chunk = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $chunk);
```

SPIP does it with:
```php
  $letexte = preg_replace(',(<p\b.*>)\s*,UiS'.$u, '\1',$letexte);
  $letexte = preg_replace(',\s*(</p\b.*>),UiS'.$u, '\1',$letexte);
  $letexte = preg_replace(',<p\b[^<>]*></p>\s*,iS'.$u, '', $letexte);
  $letexte = str_replace('<p >', "<p$class_spip>", $letexte);
```

OK, you got it... and this is the same over and over, _ad nauseam._ Paragraph formatting, XSS security clearance, emphasis, links, images, and so on. Every project seems to be writing its own lists of sometimes less-than-optimal code to do the same stuff.

~

I propose everyone does this:

```php
use TextWheel\TextWheel;

$textwheel = new TextWheel('/path/to/rules.yml');
$text = $textwheel->process($text);
```

OK now, what's going to be boring is that, at some point, someone will have to rewrite all those engines into lists of rules. But then lists of rules will be _conventional_ and _description-based_. And this will mean a lot in terms of simplicity and interoperability.

## Rules & Regulations

Regulation *#1. TextWheel is agnostic*

By itself TextWheel does not contain _any_ rule and does not favor any "shortcut syntax" over the other. The engine ships with no base ruleset (i.e., does nothing). Give it a ruleset, and it does apply all rules in sequence, as fast as possible.

Regulation *#2. A good ruleset is autonomous*

If you want to port an existing text engine to TextWheel, please try and make sure it can run independently of your application. If the ruleset needs a library, include the library. If it needs so many libraries that you need to include your whole application, then... maybe it's time to rethink your dependency model.

Rulesets can be distributed alongside TextWheel when they are autonomous.

## How to start

The simplest way to integrate your engine with TextWheel is the following:
```php
use TextWheel\TextWheel;

$ruleset['myengine'] = array(
  'match' => '/.*/',
  'replace' => 'return myengine($m[0]);',
  'create_replace' => true,
);
$textwheel = new TextWheel($ruleset);
$text = $textwheel->process($text);
```

which means "just call my function on the whole text". This might be a first step.

## Code examples

1. my ruleset

```php
$ruleset = array(
  array(
    'type' => 'str',
    'match' => 'aa',
    'replace' => 'bb',
    'if_chars' => 'a'
  ),
  array(
    'match' => '/\s+/S',
    'replace' => 'return strlen($m[0]);',
    'if_chars' => ' ',
    'create_replace' => true,
  ),
);
```

This ruleset has two rules.

The first one does a `str_replace` transforming all `'aa'` substrings to `'bb'`. It is applied only if the character `'a'` is present in the text.

The second one will apply only if the text contains a space character. If so, a replacement function will be created on the fly, then there will be a ``preg_replace_callback()`` on the `/\s+/S` expression (any sequence of spaces), call the newliy created function (which replaces a the matched substring by its character length).

2. procedural call

```php
function verynice($text)
{
    static $wheel;
    if (!isset($wheel)) {
        $wheel = new TextWheel(ruleset());
    }

    return $wheel->process($text);
}

var_dump(verynice('ab   baa z '));
```

As you might have guessed, both calls will output: `string(9) "ab3bbb1z1"`.

2. object call

```php
class VeryNice extends TextWheel {
  function VeryNice() {
    $this->addRules(ruleset());
  }
}
$wheel = new VeryNice();
var_dump($wheel->text('ab   baa z '));
```

## API and Options

- Before compiling and applying a complex regular expression, a rule can check if it's needed, with a (much) simpler expression check.
- Pattern matching can be done by ``str_replace()</code> or <code>preg_match()``.
-  Replacements can be done with string expressions or with callback functions.
- Callback functions can be created on the fly as they are needed.

OK let's have a look at the API:

A rule can be defined by a `TextWheel\Rule` object instance (but an array with the same named properties is fine):

The `TextWheel` class offer the following API:

```php
class TextWheel {
  var $rules = array();
  public function TextWheel($ruleset = array()) {}
  public function text($t) {}
  public function addRule($rule) {}
  public function addRules(array $rules) {}
}
```
