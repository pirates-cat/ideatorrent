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
 * qapoll_template-name_get_theme_options :
 * Callback function used to advertize the theme options and its default.
 * The returned data should be in the following format:
 * array(
 * 	"param_name" => array (
 * 		"type" => "string|integer|boolean|bigstring",
 *		"default_value" => default_value,
 *		"name" => A name to be used in admin pages.
 * 		"description" => description
 *		)
 * 	)
 */
function qapoll_brownie_get_theme_options()
{
	return array(
		"project_name" => array(
			"type" => "string",
			"default_value" => t("Project name"),
			"name" => t("Project name"),
			"description" => t("The name of your project to use in several part of the theme.")
			),
		"item_description_auth_tags" => array(
			"type" => "string",
			"default_value" => "<p><a>",
			"name" => t("Authorized idea description HTML tags"),
			"description" => t("The authorized HTML tags to be used in idea descriptions.")
			),
		"item_comment_auth_tags" => array(
			"type" => "string",
			"default_value" => "<p><b><i><a><embed><object>",
			"name" => t("Authorized comment HTML tags"),
			"description" => t("The authorized HTML tags to be used in idea comments.")
			),
		"choice_solution_max_visible_lines" => array(
			"type" => "integer",
			"default_value" => "20",
			"name" => t("Max visible lines of solutions"),
			"description" => t("The max number of lines of a solution to be shown by default. The rest can be seen by clicking an expand link.")
			),
		"submit_idea_first_part_description" => array(
			"type" => "bigstring",
			"default_value" => t('<div style="font-size:1.6em; text-align:center">
Welcome to the $project_name$ IdeaTorrent website!
</div>
<br />

<div style="font-size:1.2em">
Let\'s look at your request:
<ul>
<li>If your request is about something that is not working as expected, you should <b>not</b> post here, but rather use the $project_name$ bug tracker.</li>
<li>If you are asking for support, you should post on the $project_name$ forums or mailing lists, <b>not</b> here.</li>
</ul>
But if it is a feature request or an idea for $project_name$, this is the right place to post.

</div>'),
			"name" => t("Idea submission guidelines"),
			"description" => t('The text to show on the first page of idea submission. $project_name$ is replaced by the project name, $theme_path$ by the theme path')
			)
		);
}

/**
 * qapoll_template-name_load_external_files :
 * Callback function used to attach the necessary external files (CSS and JS) according to
 * the subdirectory and the template called.
 * The system path of the theme.
 */
function qapoll_brownie_load_external_files($subdir, $template, $theme_path)
{
	//List of strings to have translated for the javascript files.
	$translated_strings = array();

	//Common stuff
	drupal_add_css(drupal_get_path('module', 'qawebsite') . '/qawebsite.css');
	drupal_add_css($theme_path . '/css/qapoll.css');

	$translated_strings['search'] = t('Search...');
	$translated_strings['updating'] = t('Updating...');
	$translated_strings['nonefound'] = t('None found');
	drupal_add_js($theme_path . '/js/qapoll_common.js');
	qapoll_process_and_add_js($theme_path . '/js/qapoll_ajax.js.php');

	switch($subdir)
	{
		case "choicelist/":
			drupal_add_js($theme_path . '/js/qapoll_choicelist.js');

			$translated_strings['promotethissolution'] = t('Promote this solution!');
			$translated_strings['dontcare'] = t("Don't care");
			$translated_strings['demotethissolution'] = t('Demote this solution!');
			$translated_strings['promoted'] = t('You promoted this solution');
			$translated_strings['blankvotecasted'] = t("You cast a blank vote");
			$translated_strings['demoted'] = t('You demoted this solution');
			$translated_strings['bookmark'] = t('Bookmark this idea');
			$translated_strings['unbookmark'] = t('Remove the bookmark');
			drupal_add_js($theme_path . '/js/qapoll_vote.js');

			$translated_strings['delete'] = t('Delete');
			$translated_strings['undelete'] = t('Undelete');
			drupal_add_js($theme_path . '/js/qapoll_admin.js');
		break;

		case "choice/":
			drupal_add_js($theme_path . '/js/qapoll_popup.js');

			$translated_strings['promotethissolution'] = t('Promote this solution!');
			$translated_strings['dontcare'] = t("Don't care");
			$translated_strings['demotethissolution'] = t('Demote this solution!');
			$translated_strings['promoted'] = t('You promoted this solution');
			$translated_strings['blankvotecasted'] = t("You cast a blank vote");
			$translated_strings['demoted'] = t('You demoted this solution');
			$translated_strings['bookmark'] = t('Bookmark this idea');
			$translated_strings['unbookmark'] = t('Remove the bookmark');
			drupal_add_js($theme_path . '/js/qapoll_vote.js');
			if($template == "edit" || $template == "submit_first_part" || $template == "submit_second_part")
				drupal_add_js($theme_path . '/js/qapoll_choice.js');
			if($template == "default" || $template == "item")
			{
				$translated_strings['none'] = t('none');
				$translated_strings['loading'] = t('Loading...');
				$translated_strings['nothingothers'] = t('Nothing/Others');
				drupal_add_js($theme_path . '/js/qapoll_choice_page.js');
			}
			if($template == "report_duplicate")
				drupal_add_js($theme_path . '/js/qapoll_choice_report_duplicate.js');
		break;

		case "duplicate_reportlist/":
			$translated_strings['delete'] = t('Delete');
			$translated_strings['undelete'] = t('Undelete');
			drupal_add_js($theme_path . '/js/qapoll_admin.js');
			drupal_add_js($theme_path . '/js/qapoll_process_dup.js');
		break;

		case "user/":
			drupal_add_js($theme_path . '/js/qapoll_user.js');
		break;

		case "reportlist/":
			drupal_add_js($theme_path . '/js/qapoll_process_report.js');
		break;
	}

	//Attach the translated strings, only once.
	$new_definitions = array();
	static $definitions = array();
	foreach($translated_strings as $varname => $string)
	{
		if(isset($definitions[$varname]) == false)
		{
			$definitions[$varname] = true;
			$new_definitions[$varname] = "var i18n_" . $varname . " = " . drupal_to_js($string);
		}
	}
	if(count($new_definitions) > 0)
		drupal_add_js(implode($new_definitions, "; "), 'inline');
}

