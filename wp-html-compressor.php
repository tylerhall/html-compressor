<?PHP
/*
Plugin Name: wp-html-compressor
Plugin URI: http://github.com/tylerhall/html-compressor
Description: Attemps to reduce the filesize of an HTML document by removing unnecessary whitespace at the beginning and end of lines, inside closing tags, and stripping blank lines completely.
Version: 1.0
Author: Tyler Hall
Author URI: http://clickontyler.com
Author Email: tylerhall@gmail.com
*/

	require 'html-compressor.php';

	function wp_html_compressor()
	{
		function wp_html_compressor_main($data)
		{
			return html_compress($data);
		}
	
		ob_start('wp_html_compressor_main');
	}

	add_action('get_header', 'wp_html_compressor');
?>