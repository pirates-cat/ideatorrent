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

//Declaration of all the variables
$basepath = "";
$navigation_subtabs_prefix = "";
$navigation_subtabs_index = 0;


//Compute the prefix path we will use
$basepath = $GLOBALS['basemodule_url'] . "/";
if($GLOBALS['gbl_relation'] != null)
{
	$basepath .= $GLOBALS['gbl_relation']->getData()->url_name . "/";
	if($GLOBALS['gbl_relationsubcat'] != null)
		$basepath .= $GLOBALS['gbl_relationsubcat']->getData()->url_name . "/";
}
if($GLOBALS['gbl_category'] != null)
	$basepath .= $GLOBALS['gbl_category']->getData()->url_name . "/";
$basepath .= "ideatorrent_admin/";

$navigation_subtabs_prefix = $basepath;


//Determine which tab need to be selected
if($options["selected_page"] == "global_options")
{
	$navigation_subtabs_index = 0;
}
else if($options["selected_page"] == "theme_options")
{
	$navigation_subtabs_index = 1;
}
else if($options["selected_page"] == "categories")
{
	$navigation_subtabs_index = 2;
}
else if($options["selected_page"] == "relations")
{
	$navigation_subtabs_index = 3;
}
else if($options["selected_page"] == "releases")
{
	$navigation_subtabs_index = 4;
}



?>	

<!-- QAPoll right block starts here -->
<div class="qapoll">



<b class="ubuntu_title">
<b class="ubuntu_title1"><b></b></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title5"></b></b>


<table width="100%" class="ubuntu_title_main"><tr><td>
<h1 style="padding:10px 0px 0px 10px; margin: 0px 0px 0px 0px">
<?php echo t('IdeaTorrent administration'); ?>
</h1><br />

</td></tr></table>


<b class="ubuntu_title">
<b class="ubuntu_title5"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title1"><b></b></b></b>

<br />

<?php
	//We prepare the tabbed menu.
	$menu = array(
		t("Global options") => $navigation_subtabs_prefix,
		t("Selected theme options") => $navigation_subtabs_prefix . "selected_theme_options/",
		t("Categories") => $navigation_subtabs_prefix . "categories/",
		t("Projects") => $navigation_subtabs_prefix . "relations/",
		t("Releases") => $navigation_subtabs_prefix . "releases/"
		);


	$tabmenu_data["entries"] = $menu;
	$tabmenu_data["selected_entry"] = $navigation_subtabs_index;
	echo $this->loadTemplate("common/", "tabbedmenu", "", $tabmenu_data);
?>

<br />



<?php if($options["selected_page"] == "global_options") : ?>

	<form method="post" action="">

	<fieldset class="entryfldset">
		<legend><b><?php echo t('Default front page'); ?></b></legend>
		<div>
		<?php echo $GLOBALS['basemodule_url'] . "/"; ?> <input type="textbox" name="start_page" value="<?php echo $data["config"]["start_page"]; ?>" style="width:200px">
		</div>
		<span style="color:grey; font-size:x-small"><?php echo t('The home page of IdeaTorrent displays content from this relative URL. If unsure, keep it blank.'); ?></span>
	</fieldset>

	<fieldset class="entryfldset" name="selected_theme">
		<legend><b><?php echo t('IdeaTorrent theme'); ?></b></legend>
		<div>
		<select name="selected_theme">
		<?php foreach(View::getThemeList() as $theme) : ?>
		<option value="<?php echo $theme; ?>" <?php if($theme == $data["config"]["selected_theme"]) : ?>selected="selected"<?php endif; ?>><?php echo $theme; ?></option>
		<?php endforeach; ?>
		</select>
		</div>
		<span style="color:grey; font-size:x-small"><?php echo t('The theme engine to use to display IdeaTorrent.'); ?></span>
	</fieldset>

	<fieldset class="entryfldset">
		<legend><b><?php echo t('Number of idea per page'); ?></b></legend>
		<div>
		<input type="textbox" name="default_number_item_per_page" value="<?php echo $data["config"]["default_number_item_per_page"]; ?>" style="width:50px">
		</div>
		<span style="color:grey; font-size:x-small"><?php echo t('The number of ideas to show per page.'); ?></span>
	</fieldset>

	<fieldset class="entryfldset">
		<legend><b><?php echo t('Number of idea approvals needed'); ?></b></legend>
		<div>
		<input type="textbox" name="choice_number_approvals_needed" value="<?php echo $data["config"]["choice_number_approvals_needed"]; ?>" style="width:50px">
		</div>
		<span style="color:grey; font-size:x-small"><?php echo t('The number of approval needed for a newly submitted idea to go to the "popular ideas" area. If 0, new ideas will skip the "idea sandbox" area.'); ?></span>
	</fieldset>


	<input type="hidden" name="_config_saved" value="true" />
	<input type="submit" name="config_submit" value="<?php echo t('Save'); ?>" id="submitbutton" />
	</form>

<?php elseif($options["selected_page"] == "theme_options") : ?>

	<form method="post" action="">

	<?php foreach($data["themeconfig"] as $option_name => $option_values) : ?>

	<fieldset class="entryfldset">
	<legend><b><?php echo $option_values["name"]; ?></b></legend>
	<div>
	<?php if($option_values["type"] == "string") : ?>

	<input type="textbox" name="<?php echo $option_name; ?>" value="<?php echo (($option_values["value"] != "")?$option_values["value"]:$option_values["default_value"]); ?>" style="width:200px">

	<?php elseif($option_values["type"] == "bigstring") : ?>

	<textarea cols="60" name="<?php echo $option_name; ?>" rows="15" style="margin-top:2px"><?php echo htmlentities((($option_values["value"] != "")?$option_values["value"]:$option_values["default_value"])); ?></textarea>

	<?php elseif($option_values["type"] == "integer") : ?>

	<input type="textbox" name="<?php echo $option_name; ?>" value="<?php echo (($option_values["value"] != "")?$option_values["value"]:$option_values["default_value"]); ?>" style="width:50px">

	<?php endif; ?>
	</div>
	<span style="color:grey; font-size:x-small"><?php echo $option_values["description"]; ?></span>
	</fieldset>

	<?php endforeach; ?>

	<input type="hidden" name="_config_saved" value="true" />
	<input type="submit" name="config_submit" value="<?php echo t('Save'); ?>" id="submitbutton" />
	</form>

<?php elseif($options["selected_page"] == "categories") : ?>


	<?php echo t('Idea global categories make it easier to browse amongst ideas. If categories are defined, the idea submission form will ask users to select a category.'); ?>
	<br />
	<br />

	<form method="post" action="">

	<table>
	<tr style="border: 1px solid grey; text-align:center; color: #555555; font-weight: bold;">
	<td style="border: 1px solid grey">
		<?php echo t('Category'); ?>
	</td>
	<td style="border: 1px solid grey">
		<?php echo t('Category URL name'); ?>
	</td>
	<td style="border: 1px solid grey">
		<?php echo t('Actions'); ?>
	</td>
	</tr>
	<?php foreach($data['categories'] as $category) : ?>
	<tr>
		<td style="padding:5px; vertical-align:top">
			<?php echo $category->name; ?>
		</td>
		<td style="padding:5px; vertical-align:top">
			<?php echo $category->url_name; ?>
		</td>
		<td style="padding:5px; vertical-align:top">
			<a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/" . $category->id . "/edit/"; ?>"><?php echo t('edit'); ?></a> - <a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/" . $category->id . "/delete/"; ?>"><?php echo t('delete'); ?></a>
		</td>
	</tr>
	<?php endforeach; ?>
	<tr>
		<td style="padding:5px; vertical-align:top">
			<input type="text" name="cat_name" style="width:200px" value="<?php echo htmlentities($_POST['cat_name']); ?>">
		</td>
		<td style="padding:5px; vertical-align:top">
			<input type="text" name="cat_urlname" style="width:200px" value="<?php echo htmlentities($_POST['cat_urlname']); ?>">
		</td>
		<td style="padding:5px; vertical-align:top">
			<input type="hidden" name="_category_saved" value="true" />
			<input type="submit" value="<?php echo t('Add category'); ?>">
		</td>
	</tr>
	</table>
	</form>

<?php elseif($options["selected_page"] == "relations") : ?>

<div style="font-weight:bold; text-align:center">Not implemented yet!</div>

<?php elseif($options["selected_page"] == "releases") : ?>

	<?php echo t('Here, you can set a list of release names. If such releases names are defined, users with the administrator or developer role will be able to set a target release for an idea.'); ?>
<br />
	<?php echo t('Note that users will only be able to browse by the "advertized releases" in the "ideas in development" and "implemented ideas" pages.'); ?>
	<br />
	<br />

	<form method="post" action="">

	<table>
	<tr style="border: 1px solid grey; text-align:center; color: #555555; font-weight: bold;">
	<td style="border: 1px solid grey">
		<?php echo t('Release'); ?>
	</td>
	<td style="border: 1px solid grey">
		<?php echo t('Release URL name'); ?>
	</td>
	<td style="border: 1px solid grey">
		<?php echo t('Advertized release'); ?>
	</td>
	<td style="border: 1px solid grey">
		<?php echo t('Actions'); ?>
	</td>
	</tr>
	<?php foreach($data['releases'] as $release) : ?>
	<tr>
		<td style="padding:5px; vertical-align:top">
			<?php echo $release->long_name; ?>
		</td>
		<td style="padding:5px; vertical-align:top">
			<?php echo $release->small_name; ?>
		</td>
		<td style="padding:5px; vertical-align:top">
			<?php echo (($release->old_release == "f")?"Yes":"No"); ?>
		</td>
		<td style="padding:5px; vertical-align:top">
			<a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/" . $release->id . "/edit/"; ?>"><?php echo t('edit'); ?></a> - <a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/" . $release->id . "/delete/"; ?>"><?php echo t('delete'); ?></a>
		</td>
	</tr>
	<?php endforeach; ?>
	<tr>
		<td style="padding:5px; vertical-align:top">
			<input type="text" name="release_long_name" style="width:200px" value="<?php echo htmlentities($_POST['release_long_name']); ?>">
		</td>
		<td style="padding:5px; vertical-align:top">
			<input type="text" name="release_small_name" style="width:200px" value="<?php echo htmlentities($_POST['release_small_name']); ?>">
		</td>
		<td style="padding:5px; vertical-align:top">
			<input type="checkbox" name="release_new_release" value="<?php echo ((isset($_POST['release_new_release']))?'checked="checked"':""); ?>">
		</td>
		<td style="padding:5px; vertical-align:top">
			<input type="hidden" name="_release_saved" value="true" />
			<input type="submit" value="<?php echo t('Add release'); ?>">
		</td>
	</tr>
	</table>
	</form>

<?php endif; ?>

</div>
<!-- QAPoll right block ends here -->
