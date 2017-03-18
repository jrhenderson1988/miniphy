# Miniphy

A PHP based HTML minifier that's designed to be simple. It offers built in support for Laravel 5, providing both a service provider and facade. Additionally, it is configurable to allow for compile-time minification of blade template files.

# Installation

TODO

# Usage

It's pretty easy to use Miniphy, in fact it's just a case of creating a `Miniphy` instance and calling the `html` method to create a HTML driver to be used for minifying HTML. You can then call the `minify` method on the resulting driver with some content and it's minified content will be returned:

    use Miniphy\Miniphy;
    
    $content = '<html>    <body>   <p>    Your HTML content    </p>    </body>    </html>';
    $miniphy = new Miniphy();
    $minifiedContent = $miniphy->html()->minify($content);
    
For convenience, you may also pass your HTML content to the `html` method and the minified content will be returned. Under the hood this will create a HTML driver and call it's `minify` method.

    use Miniphy\Miniphy;
    $content = '<html>    <body>   <p>    Your HTML content    </p>    </body>    </html>';
    $minifiedContent = (new Miniphy())->html($content);
    
### Modes

Miniphy's HTML minification allows for 3 different modes: soft, medium and hard.

- *Soft (default):* This mode will leave a single space between HTML elements when removing whitespace. This is the safest mode to use and will be the least likely to cause problems.
- *Medium:* This mode will remove all whitespace around block level and undisplayed elements, but preserve a single space around inline elements such as `span`, `a` etc. Bear in mind that it's possible to make typically inline elements behave like block elements using CSS, so this mode may have unwanted side-effects if doing so.
- *Hard*: This is the most aggressive mode and will remove whitespace around all elements.

You can easily set the mode by calling the chainable `setHtmlMode` method on the `Miniphy` instance. There is also a `htmlMode` method that can be used to get or set the HTML mode, when provided with a parameter, this method will return the `Miniphy` instance for chaining. Without a parameter, this method will return an integer value representing the mode.

    use Miniphy\Miniphy;

    $miniphy = new Miniphy();
    
    // Soft mode
    $miniphy->setHtmlMode(Miniphy::HTML_MODE_SOFT);
    
    // Set medium mode and minify the provided HTML content
    $miniphy->setHtmlMode(Miniphy::HTML_MODE_MEDIUM)->html(' HTML CONTENT ');
    
    // Set hard mode and minify the content using the htmlMode method
    $miniphy->htmlMode(Miniphy::HTML_MODE_HARD)->html(' HTML CONTENT ');
    
### Laravel

When you've set up the package correctly in Laravel you can use Miniphy very easily, either through using the Facade or you can get an instance of `Miniphy` from the IoC container:

#### Facade

    $minified = Miniphy::html(' Your HTML content ');
    
#### IoC container

    $miniphy = app('miniphy');
    $minified = $miniphy->html(' Your HTML content ');

## TODO

- Add support for minifying inline CSS.
- Add support for minifying inline Javascript.
