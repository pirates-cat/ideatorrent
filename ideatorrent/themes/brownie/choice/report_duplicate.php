<?php 
/*
Copyright (C) 2007 Nicolas Deschildre <ndeschildre@gmail.com>

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

<?php if($GLOBALS['entrypoint']->getData()->filterArray['type_bug'] != null && $GLOBALS['entrypoint']->getData()->filterArray['type_bug'] == 0) : ?>







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

echo outputTabbedMenu($menu, 2); 
?>


<form method="post" action="">

<br />
<?php echo t('If you have found a duplicate idea, and would like it to be merged, please submit the idea number below.  The admins will accept or reject the merge shortly.<br />
You can use the search widget below to find more of them!'); ?>


<br />
<br />

<div>
<span style="font-weight:bold"><?php echo t('Duplicate idea number: '); ?></span><input type="text" name="dup_number" size="8" <?php if($this->_data->duplicatenumber != -1) : ?>disabled="disabled" <?php endif; ?>/>

<input type="hidden" name="_dup_submitted" value="true" />
<input type="submit" name="dup_submit" value="<?php echo t('Submit'); ?>" id="submitbutton" <?php if($this->_data->duplicatenumber != -1) : ?>disabled="disabled" <?php endif; ?>/>

</div>
</form>



 

<br />




<br />


<?php
$dupreplist = $this->_model->getDuplicateReportTargetingUsList()->items;
if(count($dupreplist) > 0) : ?>



<div style="font-weight:bold">
<?php echo t('The following duplicates have already been proposed:'); ?>
</div>


<div style="padding-left:10px; padding-top:6px">


<?php for ($i = 0; $i < count($dupreplist); $i++) : ?>

<?php if($dupreplist[$i]->status == 1 && $dupreplist[$i]->dupdupnumber == $this->_data->id) : ?>
<div>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/yes.png" alt="Accepted" title="Accepted"> <a href="<?php echo $GLOBALS['basemodule_url'] ?>/item/<?php echo $dupreplist[$i]->duplicateid; ?>/" class="undecoredlink"><?php echo t('Idea #!number: !title', array("!number" => $dupreplist[$i]->duplicateid, "!title" => strip_tags_and_evil_attributes($dupreplist[$i]->duptitle))); ?></a>

</div>
<?php endif; ?>
<?php endfor; ?>

<?php for ($i = 0; $i < count($dupreplist); $i++) : ?>
<?php if($dupreplist[$i]->status == 2 || ($dupreplist[$i]->status == 1 && $dupreplist[$i]->dupdupnumber != $this->_data->id)) : ?>
<div>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/no.png" alt="Refused" title="Refused"> <a href="<?php echo $GLOBALS['basemodule_url'] ?>/item/<?php echo $dupreplist[$i]->duplicateid; ?>/" class="undecoredlink"><?php echo t('Idea #!number: !title', array("!number" => $dupreplist[$i]->duplicateid, "!title" => strip_tags_and_evil_attributes($dupreplist[$i]->duptitle))); ?></a>
</div>
<?php endif; ?>
<?php endfor; ?>

<?php for ($i = 0; $i < count($dupreplist); $i++) : ?>
<?php if($dupreplist[$i]->status == 0) : ?>
<div>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/maybe.png" alt="Pending approval" title="Pending approval"> <a href="<?php echo $GLOBALS['basemodule_url'] ?>/item/<?php echo $dupreplist[$i]->duplicateid; ?>/" class="undecoredlink">
<?php echo t('Idea #!number: !title', array("!number" => $dupreplist[$i]->duplicateid, "!title" => strip_tags_and_evil_attributes($dupreplist[$i]->duptitle))); ?></a>
</div>
<?php endif; ?>
<?php endfor; ?>


</div>

<br />
<br />

<?php endif; ?>














<div id="reportduplicate-searcharea" style="display:none">

<form method="post" action="">


<div>

<input style="width:400px;" type="text" name="keywords" maxlength="80" id="dup_search_string" value="<?php if($_POST['keywords'] != null) echo htmlentities($_POST['keywords']); else echo force_text_wrap(strip_tags_and_evil_attributes($data["choice"]->title), 30); ?>" />

<input type="hidden" name="_keywords_submitted" value="true" />
<input type="submit" name="choice_submit" value="<?php echo t('Check for duplicates'); ?>" onclick="update_dup_table('', 1, 'dup_search_string', 'submitpage2_showNextButton'); return false;" />
</div>
</form>

<br />

<?php
	//Put the top navigation bar
	$duptable_data["duplist"] = $data['duplist'];
	echo $this->loadTemplate("common/", "duplicatetable", "", $duptable_data);

?>
</div>



<?php endif; ?>


</div>
<!-- QAPoll right block ends here -->
