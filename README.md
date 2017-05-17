# Slice-Library
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)]()

Slice-Library is a CodeIgniter library that simulates Laravel's Blade templating system! Slice-Library is also compatible with Modular Extensions - HMVC; saves the compiled template in cache and is easy to use and install.

## Features

+ Requires nearly zero configuration!
+ Easy to install and use.
+ Helps you organize your views folder.
+ 30 directives to use!
+ Does not restrict you from using plain PHP code in your views.
+ Easy to learn and to get used to.
+ Caches files until they are modified!
+ Handles complex language string lines.
+ Provides you clear, thorough documentation.

## Requirements

- PHP version 5.6 or newer is recommended.
- CodeIgniter version 2.2.1 or newer.

## Instalation

To use the Slice-Library you must first copy the file located in `application/config/slice.php` to your own `application/config/` folder. Then, edit this file according to your configurations.
Now, copy the file `application/libraries/Slice.php` to your own `application/libraries/` folder.
Finally, make sure the folder `application/cache/` has a **`0664`** permission to save the compiled templates the library will produce.

That's all! Have fun!

## Loading Slice-Library

Your can load the Slice-Library as you load any other library in CodeIgniter:

```php
$this->load->library('slice');
```

### Other Libraries and Helpers

Within Slice you can optionally load any Library and/or Helper! This is very useful if you use a library and/or a helper to handle assets, for example, and you don't want to put them in the `config/autoload.php` file.

So, to autoload any library and/or helper set the variable `enable_autoload` in the `config/slice.php` file to `TRUE`. Then add the library you want to load in the config array `libraries` and the helpers you want to load in the config array `helpers`.

This way, your libraries and helpers will be loaded automatically!

### Creating Views

Slice-Library has its own `view` method to display your HTML pages. So, to show a view you can do the following:

```php
$this->slice->view('page', ['name' => 'GustMartins']);
```

It is important to remember that your view files **MUST** have `.slice.php` as extension. But you can change that in your `application/config/slice.php` file.

As you can see, the first argument passed to the `view` method corresponds to the name of the view file in your `views` directory. The second argument is an array of data that should be made available to the view. In this case, we are passing the `name` variable, which is displayed in the view using the syntax explained bellow.

Of course, views may also be nested within sub-directories of you `views` directory. "Dot" notation may be used to reference nested views. For example:

```php
$this->slice->view('user.profile', $data);
```

### Determining If A View Exists

If you need to determine if a view exists, you may use the `exists` method. The `exists` method will return *TRUE* if the view exists:

```php
$this->slice->exists('user.email');
```

### Passing Data To Views

As you saw in the previous examples, you may pass an array of data to views:

```php

$this->slice->view('view', ['name' => 'GustMartins']);

```

When passing information in this manner, `$data` should be an array of key/value pairs. Inside your view, you can then access each value using its corresponding key, such as `<?php echo $key; ?>`. As an alternative to passing a complete array of data to the `view` method, you may use the `with` method to add individual pieces of data, or the `set` method to add an array of key/value pairs of data.

```php
$this->slice->with('name', 'GustMartins')
            ->set(['foo' => 'bar', 'users' => ['Jack', 'Kate', 'Sawyer', 'Lock', 'Jacob']])
            ->view('users');
```

If you need to append some value to a data you can use the `append` method:

```php
$this->slice->set(['foo' => 'bar', 'users' => ['Jack', 'Kate', 'Sawyer', 'Lock', 'Jacob']])
            ->append('users', 'Ben')
            ->view('users');
```

## Localization

Slice's localization features provide a convenient way to retrieve strings in various languages, allowing you to easily support multiple languages within your application.

If you want to support multiple languages in your application, you would provide folders inside your `application/language/` directory for each of them, and you would specify the default language in your `application/config/config.php`:

```plain
  application/
              language/
                      english/
                              error_lang.php
                              message_lang.php
                      french/
                              error_lang.php
                              message_lang.php
```

### Configuring The Locale

The default language for your application is stored in the `application/config/config.php` file. Of course, you may modify this value to suit the needs of your application. You may also change the active language using the `locale` method:

```php
$this->slice->locale('french');
```

### Retrieving Translation Strings

You may retrieve lines from language files using the `@lang` directive. The `@lang` accepts the file and key of the translation string as its first argument. For example, let's retrieve the welcome translation string from the `application/language/english/message_lang.php` language file:

```php
@lang('message.welcome')
```

If the specified translation string does not exist, the `@lang` directive will simply return the translation string key. So, using the example above, the `@lang` directive would return '*messages.welcome*' if the translation string does not exist.

### Replacing Parameters In Translation Strings

If you wish, you may define place-holders in your translation strings. All place-holders are prefixed with a `:`. For example, you may define a welcome message with a place-holder name:

```php
$lang['welcome'] = 'Welcome, :Name';
```

To replace the place-holders when retrieving a translation string, pass an array of replacements as the second argument to the `@lang` directive:

```php
@lang('messages.welcome', ['name' => 'GustMartins'])
```

If your place-holder contains all capital letters, or only has its first letter capitalized, the translated value will be capitalized accordingly:

```php
$lang['welcome'] = 'Welcome, :NAME';  //  Welcome, GUSTMARTINS
$lang['goodbye'] = 'Goodbye, :Name';  //  Goodbye, Gustmartins
```

### Pluralization

Pluralization is a complex problem, as different languages have a variety of complex rules for pluralization. By using a "pipe" character, you may distinguish singular and plural forms of a string:

```php
$lang['apples'] = 'There is one apple|There are many apples';
```

You may even create more complex pluralization rules which specify translation strings for multiple number ranges:

```php
$lang['apples'] = '{0} There are none|[1,19] There are some|[20,*] There are many';
```

After defining a translation string that has pluralization options, you may use the `@choice` directive to retrieve the line for a given "count". In this example, since the count is greater than one, the plural form of the translation string is returned:

```php
@choice('messages.apples', 10)
```

## Slice Syntax

Just like Laravel's Blade, Slice-Library is simple, and powerful! 
Slice-Library does not restrict you from using plain PHP code in your views. All  Slice views are compiled into plain PHP code and cached until they are modified, meaning Slice adds essentially zero overhead to your application. Slice view files use the `.slice.php` file extension, but you can change it in the `application/config/slice.php` file.

### Defining a Layout

To get started, let's take a look at a simple example. First, we will examine a "master" page layout. Since most web applications maintain the same general layout across various pages, it's convenient to define this layout as a single Slice view:

```HTML
<!-- Stored in application/views/layouts/app.slice.php -->
<html>
    <head>
        <title>App Name - @yield('title')</title>
    </head>
    <body>
        @section('sidebar')
            This is the master sidebar.
        @show

        <div class="container">
            @yield('content')
        </div>
    </body>
</html>
```

The `@section` directive, as the name implies, defines a section of content, while the `@yield` directive is used to display the contents of a given section.

### Extending A Layout

When defining a child view, use the Slice `@extends` directive to specify which layout the child view should "inherit".

```HTML
<!-- Stored in application/views/child.slice.php -->

@extends('layouts.app')

@section('title', 'Child Page Title')

@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')
    <p>This is my body content.</p>
@endsection
```

In this example, the sidebar section is utilizing the `@parent` directive to append (rather than overwriting) content to the layout's sidebar. The `@parent` directive will be replaced by the content of the layout when the view is rendered.

Now, you just need to load the view with the Slice `view` method:

```php
$this->slice->view('child');
```

### Displaying Data

You may display data passed to your Slice views by wrapping the variable in curly braces. For example, you may display the contents of a `name` variable like so:

```html
Hello, {{ $name }}.
```

Of course, you are not limited to displaying the contents of the variables passed to the view. You may also echo the results of any PHP function as well as CodeIgniter loaded library's functions:

```html
Find out more in this {{ anchor('anchor/this', 'link') }}
```

### Echoing Data If It Exists

Sometimes you may wish to echo a variable, but you aren't sure if the variable has been set. But, instead of writing a ternary statement, Slice provides you with the following convenient shortcut, which will be compiled to the ternary statement:

```html
{{ $name or 'Default' }}
```

In this example, if the `$name` variable exists, its value will be displayed. However, if it does not exist, the word `Default` will be displayed.

### Slice & JavaScript Frameworks

Since many JavaScript frameworks also use "curly" braces to indicate a given expression should be displayed in the browser, you may use the `@` symbol to inform the Slice-Library rendering engine an expression should remain untouched. For example:

```html
<h1>Slice Library</h1>

Hello, @{{ username }}
```

In this example, the `@` symbol will be removed by Slice; however, `{{ username }}` expression will remain untouched by the Slice-Library engine, allowing it to instead be rendered by your JavaScript framework.

### If Statements

You may construct `if` statements using the `@if`, `@elseif`, `@else`, and `@endif` directives. These directives function identically to their PHP counterparts:

```php
@if (count($records) === 1)
    I have one record!
@elseif (count($records) > 1)
    I have multiple records!
@else
    I don't have any records!
@endif
```

Slice-Library also provides an `@unless` directive:

```php
@unless (count($users) != 0)
    There are no users to show.
@endunless
```

In addition to the conditional directives already discussed, you may use the `@isset` and `@empty` directives as convenient shortcuts for their respective PHP functions:

```php
@isset($rows)
  //  $rows is defined and is not null...
@endisset

@empty($rows)
  //  $rows is "empty"...
@endempty
```

### Loops

Slice-Library provides simple directives for working with PHP's loop structures. Again, each of these directives functions identically to their PHP counterparts:

```php
@for ($i = 0; $i < 10; $i++)
    The current value is {{ $i }}
@endfor

@foreach ($users as $key => $user)
    <p>This is user {{ $user }}</p>
@endforeach

@forelse ($users as $key => $user)
    <li>{{ $user }}</li>
@empty
    <p>No users</p>
@endforelse

@while (true)
    <p>I'm looping forever.</p>
@endwhile
```

When using loops you may also end the loop or skip the current iteration:

```php
@foreach ($users as $key => $user)
    @if ($user == 'Kate')
        @continue
    @endif

    <li>{{ $user }}</li>

    @if ($key == 4)
        @break
    @endif
@endforeach
```

You may also include the condition with the directive declaration in one line:

```php
@foreach ($users as $key => $user)
    @continue($key == 1)

    <li>{{ $user }}</li>

    @break($key == 5)
@endforeach
```

### Comments

Slice-Libray also allows you to define comments in your views. However, unlike HTML comments, Slice comments are not included in the HTML returned by your application:

```php
{{-- This comment will not be present in the rendered HTML --}}
```

### PHP

In some situations, it's useful to embed PHP code into your views. You can use the Slice `@php` directive to execute a block of plain PHP within your template:

```php
@php
    //
@endphp
```

### Including Sub-Views

Slice's `@include` directive allows you to include a Slice view from within another view. All variables that are available to the parent view will be made available to the included view:

```php
<div>
    @include('public.errors')

    <form>
        <!-- Form Contents -->
    </form>
</div>
```

Even though the included view will inherit all data available in the parent view, you may also pass an array of extra data to the included view:

```php
@include('view.name', ['some' => 'data'])
```

Of course, if you attempt to `@include` a view which does not exist, Slice-Library will throw an error. If you would like to include a view that may or may not be present, you should use the @includeIf directive:

```php
@includeIf('view.name', ['some' => 'data'])
```

### Rendering Views For Collections

It is very useful to combine loops and includes into one line and it is possible with Slice's `@each` directive:

```php
@each('view.name', $cars, 'car')
```

The first argument is the view partial to render for each element in the array. The second argument is the array you wish to iterate over, while the third argument is the variable name that will be assigned to the current iteration within the view.

In the example, if you are iterating over an array of `cars`, typically you will want to access each car as a `car` variable within your view partial.

The key for the current iteration will be available as the `key` variable within your view partial.

You may also pass a fourth argument to the `@each` directive. This argument determines the view that will be rendered if the given array is empty.

```php
@each('view.name', $cars, 'car', 'view.empty')
```

### Extending Slice-Library Directives

Slice-Library allows you to define your own custom directives using the `directive` method. When the Slice compiler encounters the custom directive, it will call the provided callback with the expression that the directive contains.

The following example creates a @slice('Text') directive which echo a given string:

```php
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      $this->load->library('slice');
   }

   public function index()
   {
      $this->slice->with('username', 'John Doe')
                  ->directive('Test::custom_slice_directive')
                  ->view('users.profile');
   }

   static function custom_slice_directive($content)
   {
      // Finds for @slice directive
      $pattern = '/(\s*)@slice\s*\((\'.*\')\)/';
      return preg_replace($pattern, '$1<?php echo "$2"; ?>', $content);
   }
}
```

## Contributions

This package was created by [Gustavo Martins][GustMartins], but your help is welcome! Things you are welcome to do:

+ Report any bug you may encounter
+ Suggest a feature for the project

For more information about contributing to the project please, read the [Contributing Requirements][contrib].

## Change Log

Currently, the Slice-Library is in the version **1.2.0**. See the full [Changelog][changelog] for more details.

[GustMartins]: https://github.com/GustMartins
[contrib]: https://github.com/GustMartins/Slice-Library/blob/master/contributing.md
[changelog]: https://github.com/GustMartins/Slice-Library/blob/master/changelog.md
