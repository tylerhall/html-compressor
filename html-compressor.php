<?PHP
	// HTML Compressor 1.0.0
	// Original Author: Tyler Hall <tylerhall@gmail.com>
	// Latest Source and Bug Tracker: http://github.com/tylerhall/html-compressor
	//
	// Attemps to reduce the filesize of an HTML document by removing unnecessary
	// whitespace at the beginning and end of lines, inside closing tags, and
	// stripping blank lines completely. <pre> tags are respected and their contents
	// are left alone. Warning, nested <pre> tags may exhibit unexpected behaviour.
	//
	// This code is licensed under the MIT Open Source License.
	// Copyright (c) 2010 tylerhall@gmail.com
	// Permission is hereby granted, free of charge, to any person obtaining a copy
	// of this software and associated documentation files (the "Software"), to deal
	// in the Software without restriction, including without limitation the rights
	// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	// copies of the Software, and to permit persons to whom the Software is
	// furnished to do so, subject to the following conditions:
	//
	// The above copyright notice and this permission notice shall be included in
	// all copies or substantial portions of the Software.
	//
	// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	// THE SOFTWARE.

	if(defined('STDIN'))
	{
		$shortops = 'sxvoc';
		$longopts = array('stats', 'extra', 'help', 'version', 'overwrite', 'no-comments');
		$options = getopt($shortops, $longopts);

		if(array_key_exists('help', $options))
		{
			echo "Usage: " . $GLOBALS['argv'][0] . " [OPTION]... [FILE]\n";
			echo "Reduce the filesize of an HTML document by removing unnecessary whitespace.\n";
			echo "\n";
			echo "-c, --no-comments  removes HTML comments\n";
			echo "-o, --overwrite    overwrite input file with compressed version\n";
			echo "-s, --stats        output filesize savings calculation\n";
			echo "-x, --extra        perform extra (possibly unsafe) compression operations\n";
			echo "    --help         display this help and exit\n";
			echo "-v, --version      print version information and exit\n";
			echo "\n";
			echo "Report bugs to <tylerhall@gmail.com>.\n";
			exit;
		}

		if(array_key_exists('v', $options) || array_key_exists('version', $options))
		{
			echo "HTML Compressor 1.0.0\n";
			echo "<http://github.com/tylerhall/html-compressor/>\n";
			echo "\n";
			echo "This is free software; see the source for copying conditions. There is NO\n";
			echo "warranty; not even for MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n";
			exit;
		}

		if($GLOBALS['argc'] > 1 && is_readable($GLOBALS['argv'][$GLOBALS['argc'] - 1]))
			$fp = fopen($GLOBALS['argv'][$GLOBALS['argc'] - 1], 'r');
		else
			$fp = fopen('php://stdin', 'r');

		echo html_compress($fp, $options);
	}

	// $data is either a handle to an open file, or an HTML string
	function html_compress($data, $options = null)
	{
		if(!isset($options))
		{
			$options = array();
		}

		$data 			.= "\n";
		$out        = '';
		$inside_pre = false;
		$bytecount  = 0;

		while($line = get_line($data))
		{
			$bytecount += strlen($line);

			if(!$inside_pre)
			{
				if(strpos($line, '<pre') === false)
				{
					// Since we're not inside a <pre> block, we can trim both ends of the line
					$line = trim($line);
					
					// And condense multiple spaces down to one
					$line = preg_replace('/\s\s+/', ' ', $line);
				}
				else
				{
					// Only trim the beginning since we just entered a <pre> block...
					$line = ltrim($line);
					$inside_pre = true;

					// If the <pre> ends on the same line, don't turn on $inside_pre...
					if((strpos($line, '</pre') !== false) && (strripos($line, '</pre') >= strripos($line, '<pre')))
					{
						$line = rtrim($line);
						$inside_pre = false;
					}
				}
			}
			else
			{
				if((strpos($line, '</pre') !== false) && (strripos($line, '</pre') >= strripos($line, '<pre')))
				{
					// Trim the end of the line now that we found the end of the <pre> block...
					$line = rtrim($line);
					$inside_pre = false;
				}
			}

			// Filter out any blank lines that aren't inside a <pre> block...
			if($inside_pre || $line != '')
			{
				$out .= $line . "\n";
			}
		}
		
		// Remove HTML comments...
		if(array_key_exists('c', $options) || array_key_exists('no-comments', $options))
		{
			$out = preg_replace('/(<!--.*?-->)/ms', '', $out);
			$out = str_replace('<!>', '', $out);
		}

		// Perform any extra (unsafe) compression techniques...
		if(array_key_exists('x', $options) || array_key_exists('extra', $options))
		{
			// Can break layouts that are dependent on whitespace between tags
			$out = str_replace(">\n<", '><', $out);
		}

		// Remove the trailing \n
		$out = trim($out);

		// Output either our stats or the compressed data...
		if(array_key_exists('s', $options) || array_key_exists('stats', $options))
		{
			$echo = '';
			$echo .= "Original Size: $bytecount\n";
			$echo .= "Compressed Size: " . strlen($out) . "\n";
			$echo .= "Savings: " . round((1 - strlen($out) / $bytecount) * 100, 2) . "%\n";
			echo $echo;
		}
		else
		{
			if(array_key_exists('o', $options) || array_key_exists('overwrite', $options))
			{
				if($GLOBALS['argc'] > 1 && is_writable($GLOBALS['argv'][$GLOBALS['argc'] - 1]))
				{
					file_put_contents($GLOBALS['argv'][$GLOBALS['argc'] - 1], $out);
					return true;
				}
				else
				{
					return "Error: could not write to " . $GLOBALS['argv'][$GLOBALS['argc'] - 1] . "\n";
				}
			}
			else
			{
				return $out;
			}
		}
	}

	// Returns the next line from an open file handle or a string
	function get_line(&$data)
	{
		if(is_resource($data))
		{
			return fgets($data);
		}

		if(is_string($data))
		{
			if(strlen($data) > 0)
			{
				$pos = strpos($data, "\n");
				$return = substr($data, 0, $pos) . "\n";
				$data = substr($data, $pos + 1);
				return $return;
			}
			else
			{
				return false;
			}
		}
		
		return false;
	}
