<?php
/*
Copyright (C) 2008 Nicolas Deschildre <ndeschildre@gmail.com>

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
 * Process a javascript file with PHP and attach it to the view.
 */
function qapoll_process_and_add_js($file)
{
	//Process the js file.
	ob_start();
	include_once $file;
	$content = ob_get_contents();
	ob_end_clean();

	//Attach it to the document.
	if($content != "")
		drupal_add_js($content, 'inline');
}

/**
 * Force text wrap when long words (generally URLs) are submitted.
 * Otherwise layouts can be broken.
 * And we can't expect the CSS3 word-wrap:break-word to work everywhere yet.
 * Based on the function by "phpsales at gmail dot com" at http://fr3.php.net/wordwrap. Thanks to him!
 */

function force_text_wrap($str, $maxLength=80, $char=" "){
	$wordEndChars = array(" ", "\n", "\r", "\f", "\v", "\0");
	$count = 0;
	$newStr = "";
	$openTag = false;
	for($i=0; $i<strlen($str); $i++){

		if($str{$i} == "<"){
			$openTag = true;
			continue;
		}
		if(($openTag) && ($str{$i} == ">")){
			$openTag = false;
			continue;
		}

		if(!$openTag){
			if(!in_array($str{$i}, $wordEndChars)){//If not word ending char
				$count++;
				if($count>=$maxLength)
				{
					//if current word max length is reached
					$str = substr($str, 0, $i) . $char . substr($str,$i);
					$i++;
					$count = 0;
				}
			}
			else
			{
				//Else char is word ending, reset word char count
				$count = 0;
			}
		}

	}   
	return $str;
}

/**
 * Linkify the URLs, while taking care of the already present link tags. Urg. Return the modified string.
 */
function linkify_URLS($str)
{
	//There is maybe a faster way. If you find so, please tell me!
	//First replace the eventual link at the very beginning of the text.
	$str = ereg_replace("^(http[s]?://[^\" \n<]*)", "<a href=\"\\1\">\\1</a>", $str);

	//Then replace all the others links. Due to the nature of the regex, it should be run twice to catch
	//consecutive URLs :(
	$str = ereg_replace("([^\" \n][ \n]*)(http[s]?://[^\" \n<]*)", "\\1<a href=\"\\2\">\\2</a>", $str);
	$str = ereg_replace("([^\" \n][ \n]*)(http[s]?://[^\" \n<]*)", "\\1<a href=\"\\2\">\\2</a>", $str);
	return $str;
}

/**
 * This function tries to limit the global number of lines as it will appear on a webpage.
 * When too long, it will add the "[...]" string at the truncated text, and link it to url.
 */
function limit_number_of_lines($str, $lines=30, $url=null)
{
	$nb_lines = 0;
	$truncated_text = "";

	$tok = strtok($str, "\n");

	while($tok !== false && $nb_lines <= $lines)
	{
		$nb_lines += 1;
		$nb_lines += floor(strlen($tok)/100);
		$truncated_text .= $tok . "\n";
		$tok = strtok("\n");
	}

	if($nb_lines > $lines)
	{
		$str = $truncated_text . "\n<a href=\"" . $url . "\">[....]</a>";
	}

	return $str;
}

/**
 * This function tries to limit the global number of lines as it will appear on a webpage.
 * When too long, it will return the text separated in two in an array.
 * The first text will then be more or less equal to $lines.
 */
function split_text_number_of_lines($str, $lines=30)
{
	$nb_lines = 0;
	$truncated_text = "";
	$truncated_text2 = "";

	$tok = strtok($str, "\n");

	while($tok !== false && $nb_lines <= $lines)
	{
		$nb_lines += 1;
		$nb_lines += floor(strlen($tok)/100);
		$truncated_text .= $tok . "\n";
		$tok = strtok("\n");
	}
	while($tok !== false)
	{
		$truncated_text2 .= $tok . "\n";
		$tok = strtok("\n");
	}

	return array($truncated_text, $truncated_text2);
}

/**
 * Given a status number, the function will return the status string.
 * It takes care of overriden statuses.
 * Note: If the user is an admin, notify that the status is overriden.
 */
function getStatusString($statusid, $bugstatus, $specstatus, $specimpl, $duplnumber = -1)
{
	$status = "";

	if($duplnumber != -1)
		return " <span style=\"color:red\">" . t("Duplicate") . "</span>";

	switch($statusid)
	{
		case -2:
			$status = t("Deleted");
		break;

		case -1:
		case 0:
			$status = t("New");
		break;

		case 1:
			$status = t("Needs clarification");
		break;

		case 2:
			$status = t("In development");
		break;

		case 3:
			$status = t("Implemented");
		break;

		case 4:
			$status = t("Won't implement");
		break;

		case 5:
			$status = t("Already implemented");
		break;

		case 6:
			$status = t("Blueprint approved");
		break;

		case 7:
			$status = t("Not an idea");
		break;

		case 8:
			$status = t("Awaiting moderation");
		break;
	}


	return $status;
}


/**
 * DEPRECATED
 * Output a tabbedMenu with the given menu entries/link pair, and the given focus.
 */
function outputTabbedMenu($menu_entries, $focus_entry_number)
{
	$output="";

	if(!is_array($menu_entries) || !is_numeric($focus_entry_number))
		return $output;

	$i = 1;
	$output = "<ul class=\"TabbedMenu\">\n";
	foreach($menu_entries as $entry => $link)
	{
		if($i == $focus_entry_number)
			$output .= "<li id=\"TabbedMenuActive\">" . $entry . "</li>\n";
		else
			$output .= "<li><a href=\"" . $link . "\">" . $entry . "</a></li>\n";
		$i++;
	}
	$output .= "</ul>\n";

	return $output;
}


/**
 * DEPRECATED
 * Output a page title.
 */
function outputPageTitle($title)
{
	$output = '
<b class="ubuntu_title">
<b class="ubuntu_title1"><b></b></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title5"></b></b>


<table width="100%" class="ubuntu_title_main"><tr><td>
<h1 style="padding:10px 0px 0px 10px; margin: 0px 0px 0px 0px">' .
$title .
'</h1><br />

</td><td>
</td></tr></table>


<b class="ubuntu_title">
<b class="ubuntu_title5"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title1"><b></b></b></b>
';

	return $output;
}

/**
 * DEPRECATED
 * Output a page subtitle
 */
function outputPageSubtitle($subtitle)
{
	$output = '<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 

<table width="100%" class="ubuntu_roundnavbar_main"><tr><td style="padding-left:10px">' .
$subtitle .
'</td></tr>
</table>


<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>';

	return $output;
}

?>
