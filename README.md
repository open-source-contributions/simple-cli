# simple-cli

[![Latest Stable Version](https://img.shields.io/packagist/v/simple-cli/simple-cli.svg?style=flat-square)](https://packagist.org/packages/simple-cli/simple-cli)
[![Build Status](https://img.shields.io/travis/kylekatarnls/simple-cli/master.svg?style=flat-square)](https://travis-ci.org/kylekatarnls/simple-cli)
[![StyleCI](https://styleci.io/repos/192176915/shield?style=flat-square)](https://styleci.io/repos/192176915)
[![codecov.io](https://img.shields.io/codecov/c/github/kylekatarnls/simple-cli.svg?style=flat-square)](https://codecov.io/github/kylekatarnls/simple-cli?branch=master)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat-square)](https://github.com/simple-cli/simple-cli)

A simple CLI framework oriented object and dependencies-free.

# Create a command line program

You can add your command line program in any existing composer app, or create a new one using `composer init`.

Then add simple-cli:

```shell
composer require simple-cli/simple-cli
```

Let say your app allows to add or multiply 2 arguments and you want to call `easy-calc` in your CLI, so you need to
create an `EasyCalc` class that extends `SimpleCli\SimpleCli`.

So first check you have a PSR autoload set in your **composer.json**:
```json
"autoload": {
    "psr-4": {
        "MyVendorName\\": "src/MyVendorName/"
    }
},
```

(You may need to run `composer update` or `composer dump-autoload` to get the autoload in effect.)

Then create the class so it can be autoloaded, so with the example above, we can create the file
`src/MyVendorName/CliApp/EasyCalc.php`:

```php
<?php

namespace MyVendorName\CliApp;

use SimpleCli\SimpleCli;

class EasyCalc extends SimpleCli
{
    public function getCommands() : array
    {
        return []; // Your class needs to implement the getCommands(), we'll see later what to put in here.
    }
}
```

By default the name of the program will be calculated from the class name, here `EasyCalc` becomes `easy-calc` but
you can pick any name by adding `protected $name = 'my-custom-name';` in your class.

Now you can run from the console:

```shell
vendor/bin/simple-cli create MyVendorName\CliApp\EasyCalc
```

It will create `bin/easy-calc` for unix systems and `bin/easy-calc.bat` for Windows OS.

You can add it to **composer.json** so users can call it via composer:

```json
"bin": [
    "bin/easy-calc"
],
```

Let's test your CLI program now:

```shell
bin/easy-calc
```

![Usage](https://raw.githubusercontent.com/kylekatarnls/simple-cli/master/doc/img/usage.jpg)

As you can see, by default, simple-cli provide 2 commands: `list` (that is also the default when the user did not
choose a command) that list the commands available and `version` (that will show the version of your composer package
and version details you may add if you publish it).

Note that if you don't want to publish it, you can either customize what version should display:

```php
class EasyCalc extends SimpleCli
{
    public function getCommands() : array
    {
        return [];
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }
}
```

Or you can disable any of the default commands:

```php
class EasyCalc extends SimpleCli
{
    public function getCommands() : array
    {
        return [
            'version' => false,
        ];
    }
}
```

## Add commands

Now it's time for your CLI to get actual commands. To create an `add`
command for example, you can create a `MyVendorName\CliApp\Command\Add` class:

```php
<?php

namespace MyVendorName\CliApp\Command;

use SimpleCli\Command;
use SimpleCli\Options\Help;
use SimpleCli\SimpleCli;

/**
 * Sum arguments.
 */
class Add implements Command
{
    use Help;

    public function run(SimpleCli $cli): bool
    {
    }
}
```

Then add this command with a name in your CLI:

```php
class EasyCalc extends SimpleCli
{
    public function getCommands() : array
    {
        return [
            'add' => \MyVendorName\CliApp\Command\Add::class,
        ];
    }
}
```

If you run `bin/easy-calc` (or `bin/easy-calc list`) again, you will now
see `add` as an available command. And the comment you put in `/** */`
appears in the description. 

If you run `bin/easy-calc add --help` (or `bin/easy-calc add -h`) you will
the documentation of your command based on the options and arguments defined.
As you see, there is only `--help` option (or `-h` alias) provided by the
trait `SimpleCli\Options\Help` (we highly recommend to always use this trait
in your commands).

## Add arguments

Now let's add some argument so your command would actually do something.

```php
<?php

namespace MyVendorName\CliApp\Command;

use SimpleCli\Command;
use SimpleCli\Options\Help;
use SimpleCli\SimpleCli;

/**
 * Sum arguments.
 */
class Add implements Command
{
    use Help;

    /**
     * @argument
     *
     * The first number
     *
     * @var float
     */
    public $number1 = 0;

    /**
     * @argument
     *
     * The second number
     *
     * @var float
     */
    public $number2 = 0;

    public function run(SimpleCli $cli): bool
    {
        $cli->write($this->number1 + $this->number2);

        return true;
    }
}
```

The `@argument` annotation allows simple-cli to know it's an argument.

If you run `bin/easy-calc add --help` you will see they appear in the
help with their description, type and default value.

Now's time to execute your command:

```shell
bin/easy-calc add 2 3
```

It outputs `5` :rocket:

Note than `run()` must return a boolean:
 - `true` for successful command (exit code 0)
 - `false` for error (exit code 1)

You can also allow unlimited number of arguments using the annotation `@rest`
The *rest arguments* variable will be an array with all other arguments.

So if you have 2 `@argument` and a `@rest` then if your user call your command
with 5 arguments, the first one goes to the first `@argument`, the second one
go to the second `@argument`, and the 3 other ones go as an array to the `@rest`
argument.

Of course you can also use `@rest` with any other argument so for our `add`
command, it could be:

```php
<?php

namespace MyVendorName\CliApp\Command;

use SimpleCli\Command;
use SimpleCli\Options\Help;
use SimpleCli\SimpleCli;

/**
 * Sum arguments.
 */
class Add implements Command
{
    use Help;

    /**
     * @rest
     *
     * The numbers to sum
     *
     * @var float[]
     */
    public $numbers = [];

    public function run(SimpleCli $cli): bool
    {
        $cli->write(array_sum($this->numbers));

        return true;
    }
}
```

Now you can call with any number of arguments:

```shell
bin/easy-calc build 2 3 1.5
```

Outputs: `6.5`

## Add options

simple-cli provides 3 standard options. The `--help -h` you already know
as `SimpleCli\Options\Help` trait you can simply `use` in your commands.

But also `--quiet -q` as `SimpleCli\Options\Quiet` that allow your user
to mute the output. If you use this trait in your command and if user
pass the option `--quiet` or `-q` methods `$cli->write()` and
`$cli->writeLine()` (and all output methods) will no longer output anything.

You can also use `--verbose -v` using `SimpleCli\Options\Verbose`:
```php
<?php

namespace MyVendorName\CliApp\Command;

use SimpleCli\Command;
use SimpleCli\Options\Verbose;
use SimpleCli\SimpleCli;

/**
 * Sum arguments.
 */
class Add implements Command
{
    use Verbose;

    public function run(SimpleCli $cli): bool
    {
        // ...

        if ($this->verbose) {
            $cli->writeLine('Log some additional info', 'light_cyan');
        }

        // ...
    }
}
```

And you can create your own option using the `@option` annotation:
```php
<?php

namespace MyVendorName\CliApp\Command;

use SimpleCli\Command;
use SimpleCli\SimpleCli;

/**
 * Sum arguments.
 */
class Add implements Command
{
    /**
     * @option
     *
     * Something the command can use.
     *
     * @var string 
     */
    public $foo = 'default';

    /**
     * @option show-foo
     *
     * Whether foo should be displayed or not.
     *
     * @var bool 
     */
    public $showFoo = false;

    public function run(SimpleCli $cli): bool
    {
        if ($this->showFoo) {
            $cli->write($this->foo, 'red');
        }
        
        return true;
    }
}
```

```shell
bin/easy-calc --show-foo --foo=bar
```

Outputs: `bar` (in red).

Note than you can pass the name for the option and alias in the annotation:
`@option some-name, other-name, s, o` this mean `--some-name`, `--other-name`
`-s` and `-o` will all store the value in the same option variable.

Also note than if options are boolean type and have aliases, they can be merged.
If you have `@option show-foo, s` and `@option verbose, v` and pass `-vs` in
the CLI, both options will be `true`.

For non boolean options values can be set using `--foo bar` or `--foo=bar`,
both are valid. And options can come anywhere (before, after or between
arguments).

Finally, if you don't set a name and use the `@option` annotation alone
the option will have the same name as its variable and will have its
first letter as alias if it's available.

# API reference

In the examples above, you could see your command `run(SimpleCli $cli)`
method get a SimpleCli instance. `$cli` is your program object, an
instance of the class that extends `SimpleCli` so in the example above,
it's an instance of `EasyCalc` it means you can access from `$cli` all
methods you define in your sub-class and all methods available from
the `SimpleCli` inherited class:

<i start-api-reference></i>

### getVersionDetails(): string

Get details to be displayed with the version command.

*return* string

### getVersion(): string

Get the composer version of the package handling the CLI program.

*return* string

### autocomplete(string start): array

Get possible completions for a given start.

*param* string $start

*return* string[]

### read(promptcompletion): string

Ask the user $prompt and return the CLI input.

*param* string              $prompt
*param* array|callable|null $completion

*return* string

### isMuted(): bool

Returns true if the CLI program is muted (quiet).

*return* bool

### setMuted(bool muted): void

Set the mute state.

*param* bool $muted

### mute(): void

Mute the program (no more output).

### unmute(): void

Unmute the program (enable output).

### enableColors(): void

Enable colors support in command line.

### disableColors(): void

Disable colors support in command line.

### setEscapeCharacter(string escapeCharacter): void

Set a custom string for escape command in CLI strings.

*param* string $escapeCharacter

### setColors(array colorsarray backgrounds): void

Set colors palette.

*param* array|null $colors
*param* array|null $backgrounds

### colorize(string textstring colorstring background): string

Return $text with given color and background color.

*param* string      $text
*param* string|null $color
*param* string|null $background

*return* string

### rewind(int length): void

Rewind CLI cursor $length characters behind, if $length is omitted, use the last written string length.

*param* int|null $length

### write(string textstring colorstring background): void

Output $text with given color and background color.

*param* string      $text
*param* string|null $color
*param* string|null $background

### writeLine(string textstring colorstring background): void

Output $text with given color and background color and add a new line.

*param* string      $text
*param* string|null $color
*param* string|null $background

### rewrite(string textstring colorstring background): void

Replace last written line with $text with given color and background color.

*param* string      $text
*param* string|null $color
*param* string|null $background

### rewriteLine(string textstring colorstring background): void

Replace last written line with $text with given color and background color and re-add the new line.

*param* string      $text
*param* string|null $color
*param* string|null $background

### getName(): string

Get the name of the CLI program.

*return* string|null

### getFile(): string

Get the current program file called from the CLI.

*return* string

### getCommands(): array

Get the list of commands expect those provided by SimpleCli.

*return* array

### getAvailableCommands(): array

Get the list of commands included those provided by SimpleCli.

*return* array

### getCommand(): string

Get the selected command.

*return* string

### getParameters(): array

Get raw parameters (options and arguments) not filtered.

*return* string[]

### getParameterValue(string parameterarray parameterDefinition): 

Cast argument/option according to type in the definition.

*param* string $parameter
*param* array  $parameterDefinition

*return* string|int|float|bool|null

### getArguments(): array

Get list of current filtered arguments.

*return* array

### getExpectedArguments(): array

Get definitions of expected arguments.

*return* array[]

### getRestArguments(): array

Get the rest of filtered arguments.

*return* array

### getExpectedRestArgument(): array

Get definition for the rest argument if a @rest property given.

*return* array|null

### getOptions(): array

Get list of current filtered options.

*return* array

### getExpectedOptions(): array

Get definition of expected options.

*return* array[]

### getOptionDefinition(string name): array

Get option definition and expected types/values of a given one identified by name or alias.

*param* string $name

*return* array

### getPackageName(): string

Get the composer package name that handle the CLI program.

*return* string

### setVendorDirectory(string vendorDirectory): void

Set the vendor that should contains packages including composer/installed.json.

*param* string $vendorDirectory

### getVendorDirectory(): string

Get the vendor that should contains packages including composer/installed.json.

*return* string

### getInstalledPackages(): 

Get the list of packages installed with composer.

*return* array

### getInstalledPackage(string name): SimpleCli\Composer\InstalledPackage

Get data for a given installed package.

*param* string $name

*return* InstalledPackage|null

### getInstalledPackageVersion(string name): string

Get the version of a given installed package.

*param* string $name

*return* string

### extractClassNameDescription(string className): string

Get PHP comment doc block content of a given class.

*param* string $className

*return* string

### extractAnnotation(string sourcestring annotation): string

Extract an annotation content from a PHP comment doc block.

*param* string $source
*param* string $annotation

*return* string|null

<i end-api-reference></i>
