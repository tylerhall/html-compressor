HTML Compressor
=========

Attemps to reduce the filesize of an HTML document by removing unnecessary whitespace at the beginning and end of lines, condensing multiple spaces into one, and stripping blank lines completely. `<pre>` tags are respected and their contents are left alone. Warning, nested `<pre>` tags may exhibit unexpected behavior.

FEATURES
--------

 * Safely minifies HTML documents with no change in presentation.
 * Can be run as a stand-alone command line utility, as a function within another PHP script, or as a WordPress plugin.

COMMAND LINE USAGE
-------

Place `html-compressor.php` somewhere in your path, rename to `htmlcompressor`, and make executable.

    Usage: htmlcompressor [OPTION]... [FILE]

    -c, --no-comments  removes HTML comments
    -o, --overwrite    overwrite input file with compressed version
    -s, --stats        output filesize savings calculation
    -x, --extra        perform extra (possibly unsafe) compression operations
        --help         display this help and exit
    -v, --version      print version information and exit

    Examples:
        htmlcompressor index.html > index-minified.html
        htmlcompressor -ox index.html
        cat index.html | htmlcompressor > index-minified.html

PHP USAGE
-------

Simply `include html-compressor.php` in your PHP script and call `html_compress($string)`. You can also pass `html_compress()` an open file handle and it will compress that data until EOF.

WORDPRESS USAGE
--------

Place `wp-html-compressor.php` and `html-compressor.php` into your WordPress plugins folder and then activate in your WordPress admin panel. It's worth noting that this plugin _is_ compatible with [WP Super Cache](http://ocaoimh.ie/wp-super-cache/).

DEMO
-------

To see a live demo of the HTML compressor visit [my website](http://clickontyler.com) and view the page source. All pages are being run through the WordPress plugin using the default options.

UPDATES
-------

Code is hosted at GitHub: [http://github.com/tylerhall/html-compressor](http://github.com/tylerhall/html-compressor)

LICENSE
-------

The MIT License

Copyright (c) 2010 Tyler Hall &lt;tylerhall AT gmail DOT com&gt;

[http://clickontyler.com](http://clickontyler.com)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
