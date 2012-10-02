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

//
// Informations on the incoming data
// $data["show_report_links"]: -1 => Don't show the report links


//Let's compute if the solution should be shown as non-selected (0), selected(1) or nothing (-1)
$solution_selected = -1;
if($data["item"]->status == ChoiceModel::$choice_status["workinprogress"] ||
		$data["item"]->status == ChoiceModel::$choice_status["done"])
{
	$solution_selected = 0;

	$at_least_one_selected_choice = false;
	if($data["item"]->status == ChoiceModel::$choice_status["workinprogress"] ||
		$data["item"]->status == ChoiceModel::$choice_status["done"])
		foreach($data["item"]->solutions as $solution)
			if($solution->selected)
				$at_least_one_selected_choice = true;
	if($data["item_solution"]->selected || 
		$at_least_one_selected_choice == false)
	$solution_selected = 1;

}

?>


<tr>



<td style="vertical-align:top; width:1%">
		
<table style="width:100%" class="choicelisting" style="padding-left:0px">
<tr><td style="padding: 0px 5px 0px 0px">

<?php 
	//Put a vote box here.
	$item_data["item_solution"] =& $data["item_solution"];
	$item_data["item"] =& $data["item"];
	echo $this->loadTemplate("common/", "votebox", "", $item_data);
?>

</td></tr>	

</table>
</td>


<td style="vertical-align:top; padding-left:0px">

<form method="post" action="<?php echo $GLOBALS['basemodule_url'] . "/edit/"; ?>">

<div class="solutiontitle">


<?php if($solution_selected == 1) : ?>

	<?php echo t('Selected solution (#!number):', 
		array("!number" => $data["item_solution"]->solution_number)); ?>

<?php elseif($solution_selected == 0) : ?>

	<span class="nonselected">
		<?php echo t('Solution #!number:', 
		array("!number" => $data["item_solution"]->solution_number)); ?></span>

<?php else : ?>

	<?php echo t('Solution #!number:', 
		array("!number" => $data["item_solution"]->solution_number)); ?>

<?php endif; ?>

<span <?php echo (($solution_selected == 0)?"class=\"nonselected\"":""); ?> id="solution-title-<?php echo $data["item_solution"]->id; ?>"><?php echo strip_tags_and_evil_attributes($data["item_solution"]->title); ?> 

<?php if($models["item"] != null && UserModel::currentUserHasPermission("edit_solution", "Choice", $models["item"]) ||
	$models["itemlist"] != null && UserModel::currentUserHasPermission("edit_solution", "Choice", $models["itemlist"], $data["item"]->id) ||
		UserModel::currentUserHasPermission("edit_solution", "ChoiceSolution", $models["item_solutionlist"], $data["item_solution"]->id)) : ?>

<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["item"]->id . "/edit/"; ?>" onclick="showSolutionEditingArea(<?php echo $data["item_solution"]->id; ?>); return false;"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" alt="edit" title="<?php echo t('Edit the solution'); ?>"></a>

<?php endif; ?>

</span>

<input type="text" name="solution_title" maxlength="80" style="margin-top:2px; width:400px; display:none" value="<?php echo htmlentities($data["item_solution"]->title, ENT_QUOTES, "UTF-8"); ?>" id="solution-title-edit-<?php echo $data["item_solution"]->id; ?>">



</div>

<div style="padding-top:0; color:rgb(100,100,100); font-size:x-small;">
<?php 
	echo t('Written by !name the !date at !time.',
		array("!name" => '<a class="authorlink" href="' . $GLOBALS['basemodule_url'] . "/contributor/" . $data["item_solution"]->username  . '/">' .
			$data["item_solution"]->username . '</a>',
			"!date" => date('j M y',strtotime($data["item_solution"]->date)),
			"!time" => date('H:i',strtotime($data["item_solution"]->date)) ));
?> 

<?php if(user_access($GLOBALS['site']->getData()->userrole) && $data["show_report_links"] != "-1") : ?>

<?php 
	//Don't show report links if we are in dev or done or other quarantined states, since user won't be able to post solution here
	if($data["item"]->status != ChoiceModel::$choice_status["workinprogress"] &&
		$data["item"]->status != ChoiceModel::$choice_status["done"] && 
		$data["item"]->status != ChoiceModel::$choice_status["awaiting_moderation"] && 
		$data["item"]->status != ChoiceModel::$choice_status["not_an_idea"] && 
		$data["item"]->status != ChoiceModel::$choice_status["already_done"] && 
		$data["item"]->duplicatenumber == -1) : ?>

	<?php 
	echo t('Report as <a href="!spamlink">spam</a> / <a href="!irrelevancelink">irrelevant</a>',
		array("!spamlink" => $GLOBALS['basemodule_url'] . "/edit/report_spam_solution/" . $data['item_solution']->id . "/?destination=" . 
				$GLOBALS['basemodule_path'],
			"!irrelevancelink" => $GLOBALS['basemodule_url'] . "/edit/report_irrelevant_solution/" . $data['item_solution']->id . "/?destination=" . 
				$GLOBALS['basemodule_path'] ));
	?> 
	<?php endif; ?>

<?php endif; ?>


<?php if($models["item"] != null && UserModel::currentUserHasPermission("mark_solution_dup", "Choice", $models["item"]) ||
	$models["itemlist"] != null && UserModel::currentUserHasPermission("mark_solution_dup", "Choice", $models["itemlist"], $data["item"]->id)) : ?>
<div class="dupballoon">
<div>
<form action="boo">
<b><?php echo t('Mark as duplicate of solution #'); ?></b> <input type="text" name="duplicate_of" style="width:30px"><input type="submit" style="width:30px; margin-left:5px" value="OK">
</form>
</div><a rel="external" href="#">[<?php echo t('Mark as duplicate'); ?>]</a></div>
<?php endif; ?>

<?php if($models["item"] != null && UserModel::currentUserHasPermission("delete_solution", "Choice", $models["item"]) ||
	$models["itemlist"] != null && UserModel::currentUserHasPermission("delete_solution", "Choice", $models["itemlist"], $data["item"]->id)) : ?>
<a id="deletelink-<?php echo $data["item_solution"]->id; ?>" class="adminlink" href="<?php echo $GLOBALS['basemodule_url'] ?>/edit/delete_solution_link/<?php echo $data["item"]->id; ?>/<?php echo $data["item_solution"]->id; ?>/?destination=<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>">[<?php echo t('Delete rationale&lt;-&gt;solution link'); ?>]</a>
<a id="deletelink-<?php echo $data["item_solution"]->id; ?>" class="adminlink" href="<?php echo $GLOBALS['basemodule_url'] ?>/edit/delete_solution/<?php echo $data["item"]->id; ?>/<?php echo $data["item_solution"]->id; ?>/?destination=<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>">[<?php echo t('Delete'); ?>]</a>
<?php endif; ?>

<?php if($data["item"]->status == ChoiceModel::$choice_status["workinprogress"] || $data["item"]->status == ChoiceModel::$choice_status["done"]) : ?>
<?php if($models["item"] != null && UserModel::currentUserHasPermission("select_solution", "Choice", $models["item"]) ||
	$models["itemlist"] != null && UserModel::currentUserHasPermission("select_solution", "Choice", $models["itemlist"], $data["item"]->id)) : ?>
<a id="selectsolution-<?php echo $data["item_solution"]->id; ?>" class="adminlink" href="<?php echo $GLOBALS['basemodule_url'] ?>/edit/<?php if($data["item_solution"]->selected) echo "unselect"; else echo "select"; ?>_solution/<?php echo $data["item"]->id; ?>/<?php echo $data["item_solution"]->id; ?>/?destination=<?php echo $GLOBALS['basemodule_path']; ?>">[<?php if($data["item_solution"]->selected == true) echo "Unselect"; else echo "Select"; ?> solution]</a>
<?php endif; ?>
<?php endif; ?>


</div>

<div id="solution-description-<?php echo $data["item_solution"]->id; ?>" <?php echo (($solution_selected == 0)?"class=\"nonselected\"":""); ?>>
<?php 

//Text contains the text separated in two if it is > 30 lines.
$text = split_text_number_of_lines(force_text_wrap(linkify_URLS(strip_tags_and_evil_attributes($data["item_solution"]->description, $this->getThemeSetting("item_description_auth_tags")))), (($solution_selected == 0)?1:$this->getThemeSetting("choice_solution_max_visible_lines")));
$text[0] = str_replace("\n", "<br />", $text[0]);
$text[1] = str_replace("\n", "<br />", $text[1]);

echo $text[0];

?>
<?php if($text[1] != "") : ?>

<div id="hidden-choice-solution-link-<?php echo $data["item_solution"]->id; ?>" class="hidden-choice-solution-link">
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $data["item"]->id; ?>/" onclick="showSecondPart(<?php echo $data["item_solution"]->id; ?>); return false;">[...]</a>
</div>

<div id="hidden-choice-solution-<?php echo $data["item_solution"]->id; ?>" class="hidden-choice-solution">
<?php echo $text[1]; ?>
</div>

<?php endif; ?>
</div>

<div id="solution-description-edit-<?php echo $data["item_solution"]->id; ?>" style="display:none">
<textarea cols="60" name="solution_text" rows="15" onKeyPress="limitText(this,5000);" style="margin-top:2px"><?php echo htmlentities($data["item_solution"]->description, ENT_QUOTES, "UTF-8"); ?></textarea>
<br />
<br />
<input type="hidden" name="solution-id" value="<?php echo $data["item_solution"]->id; ?>" />
<input type="hidden" name="choice-id" value="<?php echo $data["item"]->id; ?>" />
<input type="hidden" name="destination" value="<?php echo $GLOBALS['basemodule_path'] ?>" />
<input type="hidden" name="_solution_edited" value="true" />
<input type="submit" value="Save" style="width:100px" />
</div>


</form>

</td>





</tr>

