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



	<div style="font-size:25px; padding-left:10px"><?php echo t('Edit release !name', array("!name" => $data['release']->long_name)); ?></div>
	<br />

	<form method="post" action="">

	<fieldset class="entryfldset">
		<legend><b><?php echo t('Release name'); ?></b></legend>
		<div>
		<input type="textbox" name="release_long_name" value="<?php echo htmlentities($data['release']->long_name); ?>" style="width:200px">
		</div>
		<span style="color:grey; font-size:x-small"><?php echo t('The name of the release to display.'); ?></span>
	</fieldset>

	<fieldset class="entryfldset">
		<legend><b><?php echo t('Release URL name'); ?></b></legend>
		<div>
		<input type="textbox" name="release_small_name" value="<?php echo htmlentities($data['release']->small_name); ?>" style="width:200px">
		</div>
		<span style="color:grey; font-size:x-small"><?php echo t('The name of the release to use in URLs (e.g. http://localhost/implemented_ideas/my_release/).'); ?></span>
	</fieldset>

	<fieldset class="entryfldset">
		<legend><b><?php echo t('Release advertized'); ?></b></legend>
		<div>
		<input type="checkbox" name="release_new_release" value="<?php echo (($data['release']->old_release == "f")?'checked="checked"':""); ?>">
		</div>
		<span style="color:grey; font-size:x-small"><?php echo t('Do we want to see that release in the tab menu of the main in development/implemented ideas pages?'); ?></span>
	</fieldset>

	<fieldset class="entryfldset">
		<legend><b><?php echo t('Ordering'); ?></b></legend>
		<div>
		<select name="release_ordering">
		<?php for($i = -10; $i < 10; $i++) : ?>
		  <option value="<?php echo $i; ?>" <?php if($data['release']->ordering == $i) echo 'selected="selected"'; ?>><?php echo $i; ?></option>
		<?php endfor; ?>
		</select>
		</div>
		<span style="color:grey; font-size:x-small"><?php echo t('By default, entries are ordered alphabetically. You can override that by using this number (lowest first).'); ?></span>
	</fieldset>

	<input type="hidden" name="_release_saved" value="true" />
	<input type="submit" name="release_submit" value="<?php echo t('Save'); ?>" id="submitbutton" />
	</form>

</div>
<!-- QAPoll right block ends here -->
