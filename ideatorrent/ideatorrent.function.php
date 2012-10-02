<?php
/*
Copyright (C) 2007 Stephane Graber <stgraber@ubuntu.com>,
		2008 Nicolas Deschildre <ndeschildre@gmail.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Wrapper around db_query. Useful for debugging: set $debug to 1 to get lots of useful SQL stats
 */
$GLOBALS['ideatorrent_sql_debug'] = 0;
$GLOBALS['ideatorrent_queries'] = array();
function it_query($query)
{
	if($GLOBALS['ideatorrent_sql_debug'])
	{
		$time = explode( " ", microtime());
		$usec = (double)$time[0];
		$sec = (double)$time[1];
		$starttime = $sec + $usec;
	}

	$args = func_get_args();
	$result = db_query($query, $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);

	if($GLOBALS['ideatorrent_sql_debug'])
	{
		$time = explode( " ", microtime());
		$usec = (double)$time[0];
		$sec = (double)$time[1];
		$time = ($sec + $usec) - $starttime;
		$GLOBALS['ideatorrent_queries'][] = array("query" => $query, "time" => $time);
	}


	return $result;
}



/**
 * This function will call the PHP "require" command from the base of this module.
 */
function basemodule_require($path, $data)
{
	require $path;
	return $return_data;
}


/**
 * Explode a GET param list: from a GET param list, create a GET array.
 */
function generate_GET_array($paramlist)
{
	$items = explode("&", $paramlist);
	$count = count($items);
	$result = array();
	for($i = 0; $i < $count; $i++)
	{
		$item = explode("=", $items[$i]);
		$result[$item[0]] = $item[1];
	}

	return $result;
}


/**
 * Generate a string containing the GET param list from a _GET array.
 * Discards the q paramater (see explanation below).
 */
function generate_GET_param_list($array)
{
	//Remove the q parameter, which would lead to a routing confusion (It is used by drupal for routing).
	unset($array['q']);

	return implode_assoc("=", "&", $array);
}

function implode_assoc($glue1, $glue2, $array)
{
	if(is_array($array) == false)
		return null;

	$array2 = array();
	foreach($array as $key => $val)
		$array2[] = $key.$glue1.$val;
	return implode($glue2, $array2);
}




/**
 * Enhanced version of strip tags:
 * - Remove unwanted tags.
 * - Remove "evil" tag attributes (style, mouseover,...)
 * - If non matching pair of tags are found, put the closing tags at the end of the tect.
 */
function strip_tags_and_evil_attributes($str, $allowable_tags = "")
{
	//First strip the unwanted tags.
	$str = strip_tags($str, $allowable_tags);

	//Then remove the "evil" attributes
	$str = preg_replace('/<(.*?)>/ie', "'<'.removeEvilAttributes('\\1').'>'", $str);

	//Finally, close unclosed pair of tags.
	if($allowable_tags != "")
	{
		$allowable_tags = substr($allowable_tags, 1, strlen($allowable_tags) - 2);
		$tags = explode("><", $allowable_tags);

		foreach ($tags as $tag) {
			//Some exception. Not a full list.
			if($tag == "img" || $tag == "embed")
				continue;
			$patternopen = "/<$tag\b[^>]*>/Ui";
			$patternclose = "/<\/$tag\b[^>]*>/Ui";
			$totalopen = preg_match_all ( $patternopen, $str, $matches );
			$totalclose = preg_match_all ( $patternclose, $str, $matches2 );
			if ($totalopen > $totalclose) {
				$str .= str_repeat("</$tag>", ($totalopen - $totalclose));
			}
		}
	}

	return $str;
}

function removeEvilAttributes($tagSource)
{
	$stripAttrib = 'javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|'.
	        'onmousemove|onmouseout|onkeypress|onkeydown|onkeyup|style|class|id';
	return preg_replace(array('/javascript:[^\"\']*/i', '/[ ](' . $stripAttrib . ')[ \\t\\n]*=[ \\t\\n]*[\"\']?[^\"\'<>]*[\"\']?/i'), array('', ''), stripslashes($tagSource));
}

/**
 * Return true if input is an integer, both in real integer form or in a string.
 */
function isInteger($input){
    return(ctype_digit(strval($input)));
}

/**
 * Escape special characters so that the string can be included in XML document.
 */
function xmlencode($text)
{
	return str_replace(array("&", ">", "<", "'", "\""), array("&amp;", "&gt;", "&lt;", "&apos;", "&amp;"), $text);
}

/**
 * DEBUG FUNCTION: show the list of queries and their time of execution
 */
function it_showqueries()
{
	if($GLOBALS['ideatorrent_sql_debug'] == 0)
		return "";

	$totaltime = 0;
	foreach($GLOBALS['ideatorrent_queries'] as $query)
		$totaltime += $query['time'];

	$output = '<div style="text-align:center; font-size:16px; font-weight:bold">' . substr($totaltime, 0, 5) . ' seconds</div>';

	$output .= '<table>';

	foreach($GLOBALS['ideatorrent_queries'] as $query)
	{
		$output .= '<tr>';
		$output .= '<td style="border:1px solid grey">' . substr($query['time'], 0, 5) . "</td>";
		$output .= '<td style="border:1px solid grey">' . $query['query'] . "</td>";
		$output .= "</tr>";
	}

	$output .= "</table>";

	return $output;
}


?>
