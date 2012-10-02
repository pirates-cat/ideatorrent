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
// $data["show_admin_links"]: -1 => Don't show the admin links
// $data["show_bottom_delimiter"]: 1 => Show the bottom delimiter

?>	

	

<tr>

<td>


<table style="border-spacing: 0px 0px; width:100%">
<tr><td>

<?php if($this->item->item_comment_unread_flag == "t" && $this->item->last_comment_date == null ||
	$this->item->item_edition_unread_flag == "t" && $this->item->last_edit_date == null) : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/newitem.png" alt="<?php echo t('New idea'); ?>" title="<?php echo t('New idea'); ?>">
<?php endif; ?>
<?php if($this->item->item_comment_unread_flag == "t" && $this->item->last_comment_date != null) : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/newcomment.png" alt="<?php echo t('New comment'); ?>" title="<?php echo t('New comment'); ?>">
<?php endif; ?>
<?php if($this->item->item_edition_unread_flag == "t" && $this->item->last_edit_date != null) : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/edited.png" alt="<?php echo t('Edited idea'); ?>" title="<?php echo t('Edited idea'); ?>">
<?php endif; ?>

<?php if(user_access($site->getData()->userrole)) : ?>
<?php if($data["item"]->mybookmark == "t") : ?>
<a onclick="togglebookmark(<?php echo $data["item"]->id; ?>); return false;" href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $data["item"]->id; ?>/unbookmark/"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/bookmark.png" title="<?php echo t('Remove the bookmark'); ?>" id="bookmarkimg-<?php echo $data["item"]->id; ?>"></a>&nbsp;
<?php else : ?>
<a onclick="togglebookmark(<?php echo $data["item"]->id; ?>); return false;" href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $data["item"]->id; ?>/bookmark/"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/bookmark-0.png" title="<?php echo t('Bookmark this idea'); ?>" id="bookmarkimg-<?php echo $data["item"]->id; ?>"></a>&nbsp;
<?php endif; ?>
<?php endif; ?>

<a id="title-<?php echo $data["item"]->id; ?>" class="itemtitle <?php echo ($data["item"]->status == -2)?"itemdeleted":"itemundeleted"?>" href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $data["item"]->id; ?>/"><?php echo force_text_wrap(strip_tags_and_evil_attributes($data["item"]->title), 30); ?></a>
&nbsp;


<?php if($this->getThemeSetting("show_choice_attachments")) : ?>

	<?php if($data["item"]->specid != null) : ?>
	<?php echo qawebsite_blueprintballoon($data["item"]->specid,"<img src=\"/" . $this->getThemePath() . "/images/spec.png\" alt=\"spec\" ","left"); ?>
	<?php endif; ?>

	<?php if($data["item"]->forumid != null) : ?>
	<a href="http://ubuntuforums.org/showthread.php?t=<?php echo $data["item"]->forumid; ?>"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/forum.png" alt="forum"> </a>
	<?php endif; ?>

<?php endif; ?>


</td>

<?php if($data["item"]->status == ChoiceModel::$choice_status["awaiting_moderation"]) : ?>
<td rowspan="3" style="vertical-align:top; width:1%">

<?php
	//Put a vote box here.
	$item_data["item"] =& $data["item"];
	echo $this->loadTemplate("common/", "approvalbox", "", $item_data);
?>


</td>
<?php endif; ?>

</tr>
<tr><td style="font-size:x-small; padding-top:0; color:rgb(100,100,100); font-size:10px">

<span style="padding:0; margin:0"> 
<?php 
	echo t('Written by !name the !date at !time.',
		array("!name" => '<a class="authorlink" href="' . $options["basepath"] . "contributor/" . $data["item"]->username . '">' .
			$data["item"]->username . '</a>',
			"!date" => date('j M y',strtotime($data["item"]->date)),
			"!time" => date('H:i',strtotime($data["item"]->date)) ));
?>


<?php
//Show the global category if a relation is not selected
if($data["item"]->catname != null && $data["item"]->relation_id == -1)
	echo t("Global category: !cat.", array("!cat" => $data["item"]->catname));
?>

<?php
	//Show the related project only if we are not currently looking at listings of a specific project
	if($GLOBALS['gbl_relation'] == null) : 
?>

	<?php 
	//Show the related project if the idea is linked to one.
	if($data["item"]->relation_id != -1) 
		echo t("Related project: !project.", 
			array("!project" => "<a href=\"" . $GLOBALS['basemodule_url'] . "/" . $data["item"]->relation_url_name . "/\">" . 
				$data["item"]->relation_name . "</a>"));
	//else 
	//	echo "Related project: Nothing/Others."; 
	?>

<?php endif; ?>


<?php
	//Show the related project only if we are not currently looking at listings of a specific project
	if($GLOBALS['gbl_relationsubcat'] == null) : 
?>
	<?php
	//Show the relation subcategory name if a relation is linked to this idea.
	if($data["item"]->relation_id != -1 && $data["item"]->relationsubcatname != null)
		echo t("Category: !cat.",
			array("!cat" => $data["item"]->relationsubcatname));
	?>

<?php endif; ?>


<b><?php echo getStatusString($data["item"]->status, $data["item"]->bugstatus, $data["item"]->specstatus, $data["item"]->specdelivery, $data["item"]->duplicatenumber); ?></b>


<?php if(UserModel::currentUserHasPermission("mark_dup", "Choice", $models["itemlist"], $data["item"]->id) && $data["show_admin_links"] != -1) : ?>
<div class="dupballoon">
<div>
<form method="post" action="<?php echo $GLOBALS['basemodule_url'] . "/edit/"; ?>">
<b><?php echo t('Mark as duplicate of idea #'); ?></b> <input type="text" name="duplicate_of" style="width:50px"><input type="submit" style="width:30px; margin-left:5px" value="OK">
<input type="hidden" name="choice-id" value="<?php echo $data["item"]->id; ?>" />
<input type="hidden" name="destination" value="<?php echo $GLOBALS['basemodule_path'] ?>" />
<input type="hidden" name="_choice_edited" value="true" />
</form>
</div><a rel="external" href="#">[<?php echo t('Mark as duplicate'); ?>]</a></div>
<?php endif; ?>


<?php if((user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole)) && $data["show_admin_links"] != -1) : ?>	
	<?php if($data["item"]->status != -2) : ?>	
		<a id="deletelink-<?php echo $data["item"]->id; ?>" class="adminlink" onclick="deleteItem(<?php echo $data["item"]->id; ?>); return false;" href="<?php echo $GLOBALS['basemodule_url'] ?>/deleteitem/<?php echo $data["item"]->id; ?>/">[<?php echo t('Delete'); ?>]</a>
	<?php else : ?>
		<a id="deletelink-<?php echo $data["item"]->id; ?>" class="adminlink" onclick="undeleteItem(<?php echo $data["item"]->id; ?>); return false;" href="<?php echo $GLOBALS['basemodule_url'] ?>/undeleteitem/<?php echo $data["item"]->id; ?>/">[<?php echo t('Undelete'); ?>]</a>
	<?php endif; ?>
<?php endif; ?>


</span>

</td></tr>
<tr><td>


<div id="description-<?php echo $data["item"]->id; ?>" class="<?php echo ($data["item"]->status == -2)?"itemdeleted":"itemundeleted"?>">
<?php 

if($GLOBALS['entrypoint']->getData()->filterArray['type_bug'] != null && $GLOBALS['entrypoint']->getData()->filterArray['type_bug'] == 0)
	$pathname = "idea"; 
else if($GLOBALS['entrypoint']->getData()->filterArray['type_idea'] != null && $GLOBALS['entrypoint']->getData()->filterArray['type_idea'] == 0)
	$pathname = "bug";
else 
	$pathname = "item";


if($data["item"]->description != null)
	echo str_replace("\n", "<br />", limit_number_of_lines(force_text_wrap(linkify_URLS(strip_tags_and_evil_attributes($data["item"]->description, $this->getThemeSetting("item_description_auth_tags")))), 30, $GLOBALS['basemodule_url'] . "/idea/" . $data["item"]->id));
else
	echo "[No description]";
?>

</div>

<?php if($data["item"]->whiteboard != null) : ?>

<br />

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 
<div class="ubuntu_roundnavbar_main" style="padding-left:10px; padding-top:5px; padding-bottom:5px; padding-right:10px">


<div class= "qapoll_title2">
<?php echo t('Developer comments'); ?>
</div>
<div style="font-weight:bold">
<?php echo str_replace("\n", "<br />", linkify_URLS(strip_tags_and_evil_attributes($data["item"]->whiteboard, $this->getThemeSetting("item_description_auth_tags")))); ?>
</div>


</div>
<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>
<div style="margin-bottom:2px"></div>





<?php endif; ?>

</td></tr>
</table>



<table style="width:100%">
<tr><td style="padding-top:0px">



<table>

<?php if(count($data["item"]->solutions) == 0) : ?>

<tr><td style="text-align:center">
<span style="font-weight:bold"><?php echo t('No solutions.'); ?></span>
</td></tr>

<?php else : ?>

<?php 
//If we are in development or implemented, only show the selected solutions if any, otherwise all.
$at_least_one_selected_choice = false;
if($data["item"]->status == ChoiceModel::$choice_status["workinprogress"] ||
	$data["item"]->status == ChoiceModel::$choice_status["done"])
	foreach($data["item"]->solutions as $solution)
		if($solution->selected)
			$at_least_one_selected_choice = true;

?>

<?php for ($i = 0; $i < count($data["item"]->solutions); $i++) : ?>

<?php

	//If the idea in status work in progress or done : only show the selected idea, if any.
	if(
		(
			($data["item"]->status == ChoiceModel::$choice_status["workinprogress"] ||
			$data["item"]->status == ChoiceModel::$choice_status["done"]) && 
			($data["item"]->solutions[$i]->selected || 
			$at_least_one_selected_choice == false)
		)
		|| 
		(
			($data["item"]->status != ChoiceModel::$choice_status["workinprogress"] &&
			$data["item"]->status != ChoiceModel::$choice_status["done"])			
		)
	)
	{
		//Prepare the data and call the item solution template.
		$item_data = array();
		$item_data["item"] =& $data["item"];
		$item_data["item_solution"] =& $data["item"]->solutions[$i];
		$item_data["item_solution_pos"] = $i;
		$item_models["itemlist"] = $models["itemlist"];
		$item_models["item_solutionlist"] = $models["item_solutionlist"];
		echo $this->loadTemplate("choicelist/", "default", "item_solution", $item_data, $item_models);
	}
?>

<?php endfor; ?>

<?php endif; ?>







</table>






<br />

<?php if($data["item"]->status == ChoiceModel::$choice_status["awaiting_moderation"] && user_access($site->getData()->userrole)) : ?>

	<a href="#" onclick="showHideDupSearchArea(<?php echo $data["item"]->id; ?>); return false;"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/treeExpanded.png" alt="expand" title="<?php echo t('Search duplicates'); ?>"> <?php echo t('Check for duplicates'); ?></a>

	<br />
	<br />

	<div id="duplicate-searcharea-<?php echo $data["item"]->id; ?>" style="display:none">
	<form method="post" action="">
	<div>
	<input style="width:400px;" type="text" name="keywords" maxlength="80" id="dup_search_string-<?php echo $data["item"]->id; ?>" value="<?php if($_POST['keywords'] != null) echo htmlentities($_POST['keywords']); else echo force_text_wrap(strip_tags_and_evil_attributes($data["item"]->title), 30); ?>" />

	<input type="hidden" name="_keywords_submitted" value="true" />
	<input type="submit" name="choice_submit" value="Check for duplicates" onclick="update_dup_table(<?php echo $data["item"]->id; ?>, 1, 'dup_search_string-<?php echo $data["item"]->id; ?>'); return false;" />
	</div>
	</form>

	<br />

	<?php
		//Put the top navigation bar
		$duptable_data["duplist"] = $data['duplist'];
		$duptable_data["table_id"] = $data["item"]->id;
		echo $this->loadTemplate("common/", "duplicatetable", "", $duptable_data);

	?>
	</div>

<?php endif; ?>


<a class="commentslink" href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $pathname ?>/<?php echo $data["item"]->id; ?>/">

<?php if($data["item"]->commentscount > 0) : ?>

	<?php echo t('See the !count comments or propose a solution', array("!count" => $data["item"]->commentscount)); ?>
	<?php 
	if($this->view_options['show_latest_comment_date'] != 0 && $data["item"]->last_comment_date != null) 
	{
		echo t('(latest comment the !date at !time) ', 
			array("!date" => date('j M y',strtotime($data["item"]->last_comment_date)),
				"!time" => date('H:i',strtotime($data["item"]->last_comment_date))));
	}
	?> &gt;&gt;
<?php else : ?>
	<?php echo t('Add a comment or propose a solution'); ?> &gt;&gt;
<?php endif; ?>

</a>
<br />
<br />

<?php if($data["show_bottom_delimiter"] == 1) : ?>
<div style="border-bottom: 1px dotted rgb(210, 210, 210); width: 100%;"></div>
<?php endif; ?>

</td></tr>
</table>

</td>
</tr>

