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
if($data['choice'] == null)
	$selected_navbar_tab = "ingestation";

if($data['choice'] != null)
{
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
}
else
{
	//Compute the prefix path we will use
	$basepathnavbar = $GLOBALS['basemodule_url'] . "/";
	if($GLOBALS['gbl_relation'] != null)
	{
		$basepathnavbar .= $GLOBALS['gbl_relation']->getData()->url_name . "/";
		if($GLOBALS['gbl_relationsubcat'] != null)
			$basepathnavbar .= $GLOBALS['gbl_relationsubcat']->getData()->url_name . "/";
	}
	if($GLOBALS['gbl_category'] != null)
		$basepathnavbar .= $GLOBALS['gbl_category']->getData()->url_name . "/";
}

?>	

<!-- QAPoll starts here -->
<div class="qapoll">


<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = $selected_navbar_tab;
	$navbar_options["biglinks_prefix"] = $basepathnavbar;
	if($this->_data == null)
		$navbar_options["title"] = t('Submit your idea (Step 3 of 3)');
	else
		$navbar_options["title"] = t('Idea #!number:', array("!number" => $data["choice"]->id)) . " " . 
			force_text_wrap(strip_tags_and_evil_attributes($data["choice"]->title), 30);
	if($data['choice'] != null)
	{
		if($models['topnavbar_relation'] != null)
		{
			$navbar_models["selected_relation"] = $models['topnavbar_relation'];
			if($models["topnavbar_relation_subcat"] != null)
				$navbar_models["selected_relationsubcat"] = $models["topnavbar_relation_subcat"];
		}
		else if($models["topnavbar_category"] != null)
			$navbar_models['selected_category'] = $models["topnavbar_category"];
	}
	else
	{
		if($GLOBALS['gbl_relation'] != null)
		{
			$navbar_models["selected_relation"] = $GLOBALS['gbl_relation'];
			if($GLOBALS['gbl_relationsubcat'] != null)
				$navbar_models["selected_relationsubcat"] = $GLOBALS['gbl_relationsubcat'];
		}
		if($GLOBALS['gbl_category'] != null)
			$navbar_models['selected_category'] = $GLOBALS['gbl_category'];
	}
	echo $this->loadTemplate("common/", "navigationtopbar", "", $navbar_data, $navbar_models, $navbar_options);

?>



<br />	



<form method="post" action="">


<?php if($this->_data != null) : ?>

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
$menu[t("Edit")] = $GLOBALS['basemodule_url'] . "/idea/" . $this->_data->id . "/edit/";

echo outputTabbedMenu($menu, 4); 
?>

<?php endif; ?>







<div style="padding-left:5px">






<fieldset class="bugideafldset">
	<legend style="font-size: large"><b>
<?php 
	if($this->_data == null)
		echo t('Idea rationale: Why are you proposing this idea?');
	else
		echo	t('Idea rationale');
?>
</b></legend>





<fieldset class="entryfldset">
	<legend><b><?php echo t('Title'); ?></b> <?php if($this->_data == null) : ?><span class="fielddescription"><?php echo t('describe in a few words the idea rationale'); ?></span><?php endif; ?></legend>

	<div><input style="width:400px;" type="text" name="choice_title" id="dup_search_string" value="<?php if($this->_data != null && $_POST['_choice_submitted'] == null) echo htmlentities($this->_data->title, ENT_QUOTES, "UTF-8"); else echo htmlentities($_POST['choice_title'], ENT_QUOTES, "UTF-8"); ?>" maxlength="80" <?php 
	if($this->_data != null && (!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole) && !UserModel::currentUserHasPermission("edit_title", "Choice", $models["choice"]))) 
		echo "disabled=\"disabled\"" ?> /></div>
</fieldset>




<fieldset class="entryfldset">
	<legend><b><?php echo t('Description'); ?></b> <?php if($this->_data == null) : ?><span class="fielddescription"><?php echo t('describe what is the problem, why makes you submit this idea'); ?></span><?php endif; ?></legend>

	<div><textarea cols="60" name="choice_description" rows="15" onKeyPress="limitText(this,5000);" <?php 
	if($this->_data != null && ((!user_access($site->getData()->adminrole) && !user_access($site->getData()->moderatorrole)) && $this->_data->userid != $GLOBALS['user']->uid) && !UserModel::currentUserHasPermission("edit_description", "Choice", $models["choice"])) 
		echo "disabled=\"disabled\"" ?>><?php if($this->_data != null && $_POST['_choice_submitted'] == null) echo htmlentities($this->_data->description, ENT_QUOTES, "UTF-8"); else echo htmlentities($_POST['choice_description'], ENT_QUOTES, "UTF-8"); ?></textarea></div>
</fieldset>







<?php if (count($data["categorylist"]) != 0) : ?>
<fieldset class="entryfldset">
	<legend><b><?php echo t('Global category'); ?></b></legend>
	<table>
	<tr><td style="padding:0 0 0 0">
	<select name="choice_category" onchange="show_cat_tooltip()" id="categories" <?php if($this->_data != null && !user_access($site->getData()->userrole)) echo "disabled=\"disabled\"" ?> >
	<option value="-1" <?php 
		if(($this->_data != null && $this->_data->categoryid == -1 && $_POST['_choice_submitted'] == null)
			|| ($data['choice'] == null && $GLOBALS['gbl_category'] == null && $_POST['choice_category'] == null)
			|| $_POST['choice_category'] == -1) 
			echo "selected=\"selected\" " ?>>(<?php echo t('Please select'); ?>)</option>

<?php for ($i = 0; $i < count($data["categorylist"]); $i++) : ?>

	<option value="<?php echo $data["categorylist"][$i]->id; ?>" <?php
		if(($this->_data != null && $this->_data->categoryid == $data["categorylist"][$i]->id && $_POST['_choice_submitted'] == null)
			|| ($data['choice'] == null && $GLOBALS['gbl_category'] != null && 
				$GLOBALS['gbl_category']->getData()->id == $data["categorylist"][$i]->id && $_POST['_choice_submitted'] == null)
			|| $_POST['choice_category'] == $data["categorylist"][$i]->id)
			echo "selected=\"selected\" " ?> label="<?php echo $data["categorylist"][$i]->description; ?>"><?php echo $data["categorylist"][$i]->name; ?></option>

<?php endfor; ?>

	</select>
	</td><td id="choice_category_tooltip" style="padding-left:10px">	

	</td></tr>
	</table>
</fieldset>
<?php endif; ?>



<?php 
/**
 * Show this field if we have a list of relation, or if we are in a entrypoint that filter by relation, 
 * we show if we have a list of relation_subcategories.
 */
if (count($data["relationlist"]) != 0 && 
		($GLOBALS['entrypoint']->getData()->filterArray['relation'] == null ||
		count($this->_relation_subcategory_list) != 0)) : 
?>
<fieldset class="entryfldset">
	<legend><b><?php if($GLOBALS['entrypoint']->getData()->filterArray['relation'] != null) : ?><?php echo $this->_relation_name; ?> <?php echo t('Category'); ?><?php else : ?><?php echo t('Related project'); ?><?php endif; ?></b></legend>
	<table>
	<tr><td style="padding:0 0 0 0">

<?php if($GLOBALS['entrypoint']->getData()->filterArray['relation'] == null) : ?>

	<select name="choice_relation" id="relations" <?php if($this->_data != null && ((!UserModel::currentUserHasPermission("edit_relation", "Choice", $this->_model)))) echo "disabled=\"disabled\"" ?> onchange="updateRelationSubcategories()">

	<option value="-2" <?php 
		if($this->_data == null && $_POST['_choice_submitted'] == null) 
			echo "selected=\"selected\" " ?>>(<?php echo t('Please select'); ?>)</option>

<?php for ($i = 0; $i < count($data["relationlist"]); $i++) : ?>
	<?php
		if($i == 0 || $current_relation_cat != $data["relationlist"][$i]->relation_cat_name)
			echo "<optgroup label=\"" . $data["relationlist"][$i]->relation_cat_name . "\">";
		$current_relation_cat = $data["relationlist"][$i]->relation_cat_name;
	?>

		<option value="<?php echo $data["relationlist"][$i]->relation_id; ?>" <?php 
			if(($this->_data != null && $this->_data->relation_id == $data["relationlist"][$i]->relation_id && $_POST['_choice_submitted'] == null)
				|| ($data['choice'] == null && $GLOBALS['gbl_relation'] != null && 
					$GLOBALS['gbl_relation']->getData()->id == $data["relationlist"][$i]->relation_id && $_POST['_choice_submitted'] == null)
				|| $_POST['choice_relation'] == $data["relationlist"][$i]->relation_id) 
				echo "selected=\"selected\" " 
				?> label="<?php echo $data["relationlist"][$i]->relation_name; ?>"><?php echo $data["relationlist"][$i]->relation_name; ?></option>

	<?php
		if($i == count($data["relationlist"]) - 1 || $current_relation_cat != $data["relationlist"][$i+1]->relation_cat_name)
			echo "</optgroup>";
	?>
<?php endfor; ?>

	<option value="-1" <?php if(($this->_data != null && $_POST['_choice_submitted'] == null && $this->_data->relation_id == -1) || $_POST['choice_relation'] == -1) echo "selected=\"selected\" " ?>><?php echo t('Nothing/Others'); ?></option>

	</select>

<?php else : ?>

<input type="hidden" name="choice_relation" value="<?php echo $GLOBALS['entrypoint']->getData()->filterArray['relation']; ?>" />

<?php endif; ?>



<span id="project_category_string" <?php 
	if($GLOBALS['entrypoint']->getData()->filterArray['relation'] != null 
		|| count($this->_relation_subcategory_list) == 0 
		|| ($this->_data == null && $_POST['_choice_submitted'] == null && count($this->_relation_subcategory_list) == 0)) 
		echo "style=\"display:none\""?>><?php echo t('Category'); ?>: </span>

	<select name="choice_relation_subcategory" id="relations_subcategory" <?php if($this->_data != null && ((!UserModel::currentUserHasPermission("edit_relation_subcategory", "Choice", $this->_model)))) echo "disabled=\"disabled\"" ?> <?php 
		if($GLOBALS['entrypoint']->getData()->filterArray['relation'] == null && 
		(
			($this->_data == null && $_POST['_choice_submitted'] == null && count($this->_relation_subcategory_list) == 0) || 
			count($this->_relation_subcategory_list) == 0)
		)
			echo "style=\"display:none\""?>>

	<option value="-2" <?php if($this->_data == null && $_POST['_choice_submitted'] == -2) echo "selected=\"selected\" " ?>>(<?php echo t('Please select'); ?>)</option>
<!--	<option value="-1" <?php if(($this->_data != null && $_POST['_choice_submitted'] == null && $this->_data->relation_subcategory_id == -1) || $_POST['choice_relation_subcategory'] == -1) echo "selected=\"selected\" " ?>>Others</option>-->

<?php for ($i = 0; $i < count($this->_relation_subcategory_list); $i++) : ?>

	<option value="<?php echo $this->_relation_subcategory_list[$i]->id; ?>" <?php 
		if(
			($this->_data != null && $this->_data->relation_subcategory_id == $this->_relation_subcategory_list[$i]->id
				&& $_POST['_choice_submitted'] == null) 
			|| ($data['choice'] == null && $GLOBALS['gbl_relationsubcat'] != null && 
				$GLOBALS['gbl_relationsubcat']->getData()->id == $this->_relation_subcategory_list[$i]->id && $_POST['_choice_submitted'] == null)
			|| $_POST['choice_relation_subcategory'] == $this->_relation_subcategory_list[$i]->id)
			echo "selected=\"selected\" " ?> label="<?php echo $this->_relation_subcategory_list[$i]->name; ?>"><?php echo $this->_relation_subcategory_list[$i]->name; ?></option>

<?php endfor; ?>

	</select>




	</td><td id="choice_relation_tooltip" style="padding-left:10px">	

	</td></tr>
	</table>
</fieldset>
<?php endif; ?>

<fieldset class="entryfldset">
	<legend><b>(<?php echo t('Optional'); ?>) <?php echo t('Tags'); ?></b></legend>
	<div><input style="width:400px;" type="text" name="choice_tags"  value="<?php if($this->_data != null && $_POST['_choice_submitted'] == null) {foreach($this->_data->tags as $tag) echo $tag->name . " ";} else echo htmlentities($_POST['choice_tags'], ENT_QUOTES, "UTF-8"); ?>" maxlength="150" <?php if($this->_data != null && !user_access($site->getData()->userrole)) echo "disabled=\"disabled\"" ?> /></div>
</fieldset>




<?php if($GLOBALS['entrypoint']->getData()->filterArray['type_bug'] != null && $GLOBALS['entrypoint']->getData()->filterArray['type_bug'] == 0) : ?>

<input type="hidden" name="choice_type" value="1" />

<?php elseif($GLOBALS['entrypoint']->getData()->filterArray['type_idea'] != null && $GLOBALS['entrypoint']->getData()->filterArray['type_idea'] == 0) : ?>

<input type="hidden" name="choice_type" value="0" />

<?php else : ?>

<fieldset class="entryfldset">
	<legend><b>Type</b></legend>
	<div>
	<select name="choice_type" onchange="bugideahide()" id="bugideachoice" <?php if($this->_data != null && !user_access($site->getData()->userrole)) echo "disabled=\"disabled\"" ?>>
	<option value="-1" <?php if($_POST['choice_type'] == -1) echo "selected=\"selected\" " ?>>(<?php echo t('Please select'); ?>)</option>
	<option value="0" <?php if(($this->_data != null && $_POST['_choice_submitted'] == null && $this->_data->choicetype == 0) || ($_POST['choice_type'] == 0 && $_POST['choice_type'] != null)) echo "selected=\"selected\" " ?>><?php echo t('A bug'); ?></option>
	<option value="1" <?php if(($this->_data != null && $_POST['_choice_submitted'] == null && $this->_data->choicetype == 1) || $_POST['choice_type'] == 1) echo "selected=\"selected\" " ?>><?php echo t('An idea'); ?></option>
	</select>
	</div>
</fieldset>

<br />

<?php endif; ?>


</fieldset>









<fieldset class="bugideafldset">
	<legend style="font-size: large"><b>
<?php 
	if($this->_data != null)
		echo t('Idea solutions');
	else
		echo t('Idea solution: What do you propose to solve this problem?')
?>
</b></legend>


<?php if($this->_data == null) :?>


<fieldset class="entryfldset">
	<legend><b><?php echo t('Title'); ?></b> <?php if($this->_data == null) : ?><span class="fielddescription"><?php echo t('describe in a few words the idea solution'); ?></span><?php endif; ?></legend>
	<div><input style="width:400px;" type="text" name="solution_title" id="dup_search_string" value="<?php echo htmlentities($_POST['solution_title'], ENT_QUOTES, "UTF-8"); ?>" maxlength="80" /></div>
</fieldset>




<fieldset class="entryfldset">
	<legend><b><?php echo t('Description'); ?></b> <?php if($this->_data == null) : ?><span class="fielddescription"><?php echo t('describe how you want to solve the problem'); ?></span><?php endif; ?></legend>
	<div><textarea cols="60" name="solution_text" rows="15" onKeyPress="limitText(this,5000);" ><?php echo htmlentities($_POST['solution_text'], ENT_QUOTES, "UTF-8"); ?></textarea></div>
</fieldset>


<?php else : ?>

<?php foreach($this->_data->solutions as $solution) : ?>



<fieldset class="entryfldset">
	<legend><b>
<?php echo t('Solution #!number Title', array('!number' => $solution->solution_number)); ?></b> <?php if($this->_data == null) : ?><span class="fielddescription"><?php echo t('describe in a few words the idea solution'); ?></span><?php endif; ?></legend>

	<div><input style="width:400px;" type="text" name="solution_title-<?php echo $solution->id; ?>" id="dup_search_string" value="<?php if($this->_data != null && $_POST['_choice_submitted'] == null) echo htmlentities($solution->title, ENT_QUOTES, "UTF-8"); else echo htmlentities($_POST['solution_title-' . $solution->id], ENT_QUOTES, "UTF-8"); ?>" maxlength="80" <?php 
	if(!UserModel::currentUserHasPermission("edit_solution", "ChoiceSolution", $models["choice"]->additional_models["choicesolutionlist"], $solution->id) &&
			!UserModel::currentUserHasPermission("edit_solution", "Choice", $models["choice"]))
		echo "disabled=\"disabled\"" ?> /></div>
</fieldset>




<fieldset class="entryfldset">
	<legend><b><?php echo t('Solution #!number Description', array('!number' => $solution->solution_number)); ?></b> <?php if($this->_data == null) : ?><span class="fielddescription"><?php echo t('describe what is the problem, why makes you submit this idea'); ?></span><?php endif; ?></legend>

	<div><textarea cols="60" name="solution_text-<?php echo $solution->id; ?>" rows="15" onKeyPress="limitText(this,5000);" <?php 
	if(!UserModel::currentUserHasPermission("edit_solution", "ChoiceSolution", $models["choice"]->additional_models["choicesolutionlist"], $solution->id) &&
			!UserModel::currentUserHasPermission("edit_solution", "Choice", $models["choice"])) 
		echo "disabled=\"disabled\"" ?>><?php if($this->_data != null && $_POST['_choice_submitted'] == null) echo htmlentities($solution->description, ENT_QUOTES, "UTF-8"); else echo htmlentities($_POST['solution_text-' . $solution->id], ENT_QUOTES, "UTF-8"); ?></textarea></div>
</fieldset>

<?php endforeach; ?>

<?php endif; ?>



</fieldset>













<?php if($GLOBALS['entrypoint']->getData()->filterArray['type_idea'] == null || $GLOBALS['entrypoint']->getData()->filterArray['type_idea'] == 1) : ?>

<?php if($this->getThemeSetting("show_choice_attachments")) : ?>

<fieldset class="bugideafldset" id="ideainfos">
	<legend style="font-size: large"><b><?php echo t('Attachments'); ?> (<?php echo t('Optional'); ?>)</b></legend>
	<div>
	<?php echo t('If the idea was already discussed on a ubuntuforums.org thread, you can link it here.'); ?><br />
	<div style="padding-top:3px"></div>
<fieldset style="border:0px;margin: 0 8px 0 0;padding: 0;">
	<legend><b>(<?php echo t('Optional'); ?>) <?php echo t('Ubuntuforums.org thread URL :'); ?> </b></legend>
	<div><input style="width:400px" type="text" name="choice_threadid" value="<?php 

if($this->_data != null && $_POST['_choice_submitted'] == null)
	if($this->_data->forumid != null)
		echo "http://ubuntuforums.org/showthread.php?t=" . $this->_data->forumid;
	else
		echo "";
else 
	echo $_POST['choice_threadid'] 

?>" <?php if($this->_data != null && !user_access($site->getData()->userrole)) echo "disabled=\"disabled\"" ?>/></div>
</fieldset>

	<br /><?php echo t('If you know about a blueprint whose goal is to make this idea a reality, you can reference it here.'); ?> <span class="tipslinks">(<a href="javascript:tooltip_popup('<?php echo $GLOBALS['basemodule_url'] ?>/tooltip/2/');"><?php echo t('What is a blueprint?'); ?></a>)</span> <br />
	<div style="padding-top:3px"></div>
<fieldset style="border:0px;margin: 0 8px 0 0;padding: 0;">

	<legend><b>(<?php echo t('Optional'); ?>) <?php echo t('Blueprint URL'); ?> </b><b>:</b> </legend>
	<div><input style="width:400px;" type="text" name="choice_spec2" value="<?php if($this->_data != null && $_POST['_choice_submitted'] == null) echo htmlentities($this->_data->specid, ENT_QUOTES, "UTF-8"); else echo htmlentities($_POST['choice_spec2'], ENT_QUOTES, "UTF-8"); ?>" <?php if($this->_data != null && !user_access($site->getData()->userrole)) echo "disabled=\"disabled\"" ?>/> </div>
</fieldset>
	
	</div>
</fieldset>

<?php endif; ?>

<?php endif; ?>

<br />

<?php if($this->_data != null && UserModel::currentUserHasPermission("edit_dev_comments", "Choice", $this->_model)) : ?>
<fieldset class="entryfldset">
	<legend><b><?php echo t('Developer comments (admin-only field)'); ?></b></legend>
	<div><textarea cols="60" name="choice_whiteboard" rows="5" onKeyPress="limitText(this,5000);" ><?php if($this->_data != null && $_POST['_choice_submitted'] == null) echo htmlentities($this->_data->whiteboard, ENT_QUOTES, "UTF-8"); else echo htmlentities($_POST['choice_whiteboard'], ENT_QUOTES, "UTF-8"); ?></textarea></div>
</fieldset>

<br />

<?php endif; ?>

<input type="hidden" name="_choice_submitted" value="true" />
<input type="submit" name="choice_submit" value="<?php echo ($this->_data != null)?t("Save"):t("Submit!"); ?>" id="submitbutton" />

<br />
<br />

</div>

</form>	

</div>
<!-- QAPoll ends here -->	
