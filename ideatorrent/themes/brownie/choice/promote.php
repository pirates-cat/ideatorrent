<?php 
/*
Copyright (C) 2007-2008 Nicolas Deschildre <ndeschildre@gmail.com>

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

//
// Computations of paths & various things before outputing the theme
//

//Determine which tab we want to show
$selected_navbar_tab = "ingestation";
switch($data['choice']->status)
{
		case -1:
		case 0:
		case 4:
			$selected_navbar_tab = "popular";
		break;

		case 2:
			$selected_navbar_tab = "indev";
		break;

		case 3:
			$selected_navbar_tab = "implemented";
		break;
}

//Compute the prefix path we will use for the navbar
$basepathnavbar = $GLOBALS['basemodule_url'] . "/";
if($models['topnavbar_relation'] != null)
{
	$basepathnavbar .= $models['topnavbar_relation']->getData()->url_name . "/";
	if($models["topnavbar_relation_subcat"] != null)
		$basepathnavbar .= $models["topnavbar_relation_subcat"]->getData()->url_name . "/";
}
else if($models["topnavbar_category"] != null)
	$basepathnavbar .= $models["topnavbar_category"]->getData()->url_name . "/";


?>	

<!-- QAPoll right block starts here -->
<div class="qapoll">

<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = $selected_navbar_tab;
	$navbar_options["biglinks_prefix"] = $basepathnavbar;
	$navbar_options["title"] = t('Idea #!number:', array("!number" => $data["choice"]->id)) . " " . 
		force_text_wrap(strip_tags_and_evil_attributes($data["choice"]->title), 30);
	if($models['topnavbar_relation'] != null)
	{
		$navbar_models["selected_relation"] = $models['topnavbar_relation'];
		if($models["topnavbar_relation_subcat"] != null)
			$navbar_models["selected_relationsubcat"] = $models["topnavbar_relation_subcat"];
	}
	else if($models["topnavbar_category"] != null)
		$navbar_models['selected_category'] = $models["topnavbar_category"];
	echo $this->loadTemplate("common/", "navigationtopbar", "", $navbar_data, $navbar_models, $navbar_options);

?>



<br />






<?php
	//Show the idea status bar
	$idea_data = array();
	$item_data["choice"] =& $data["choice"];
	$idea_models = array();
	$idea_models["choice"] = $models["choice"];
	echo $this->loadTemplate("common/", "ideastatusbar", "", $idea_data, $idea_models);
?>


<?php 
$menu = array(
	t("Description") => $GLOBALS['basemodule_url'] . "/idea/" . $this->_data->id . "/",
	t("Report duplicate") => $GLOBALS['basemodule_url'] . "/idea/" . $this->_data->id . "/report_duplicate/",
	t("Help promote this idea!") => $GLOBALS['basemodule_url'] . "/idea/" . $this->_data->id . "/promote/");

if(user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole) || $this->_data->userid == $GLOBALS['user']->uid)
	$menu[t("Edit")] = $GLOBALS['basemodule_url'] . "/idea/" . $this->_data->id . "/edit/";

echo outputTabbedMenu($menu, 3); 
?>


<div style="padding-top:10px"></div>

<?php if(count($this->_model->getImageLinkList()) != 0) : ?>

<div class= "qapoll_title2">
<?php echo t('Image links'); ?>
</div>
<?php echo t('You like this idea? You want to help promote it across the web?<br />
Use these images in your forum signature and in your website!'); ?>
<br />
<br />

<?php 
$imglinklist = $this->_model->getImageLinkList();
for ($i = 0; $i < count($this->_model->getImageLinkList()); $i++) : ?>

<div style="font-weight:bold">
<?php echo $imglinklist[$i]->title; ?><?php if($imglinklist[$i]->need_update == "t") echo t(", updated every day"); ?> (<?php echo $imglinklist[$i]->img_width; ?>x<?php echo $imglinklist[$i]->img_height; ?>)
</div>

<table>
<tr>
<td></td>
</tr><tr>
<td><img src="<?php echo $GLOBALS['basemodule_url']; ?>/idea/<?php echo $this->_data->id; ?>/image/<?php echo $imglinklist[$i]->id; ?>/" /></td>
</table>

<table>
<tr>
<td><?php echo t('HTML code (to use in your website)'); ?></td>
<td><?php echo t('BB code (to use in a forum)'); ?></td>
</tr><tr>
<td style="padding-right:15px"><textarea cols="50" name="choice_description" rows="4">&lt;a href="<?php echo $GLOBALS['basemodule_url']; ?>/idea/<?php echo $this->_data->id; ?>/"&gt;
&lt;img src="<?php echo $GLOBALS['basemodule_url']; ?>/idea/<?php echo $this->_data->id; ?>/image/<?php echo $imglinklist[$i]->id; ?>/" /&gt;
&lt;/a&gt;</textarea></td>
<td><textarea cols="50" name="choice_description" rows="4">[URL=<?php echo $GLOBALS['basemodule_url']; ?>/idea/<?php echo $this->_data->id; ?>/][IMG]<?php echo $GLOBALS['basemodule_url']; ?>/idea/<?php echo $this->_data->id; ?>/image/<?php echo $imglinklist[$i]->id; ?>/[/IMG][/URL]</textarea></td>
</table>

<br />
<br />

<?php endfor; ?>

<?php endif; ?>


</div>
<!-- QAPoll right block ends here -->
