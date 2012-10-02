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
if($data['choice']->duplicatenumber != -1)
	$selected_navbar_tab = "ingestation";

//Compute the prefix path we will use for the navbar
$basepath = $GLOBALS['basemodule_url'] . "/";
if($models['topnavbar_relation'] != null)
{
	$basepath .= $models['topnavbar_relation']->getData()->url_name . "/";
	if($models["topnavbar_relation_subcat"] != null)
		$basepath .= $models["topnavbar_relation_subcat"]->getData()->url_name . "/";
}
else if($models["topnavbar_category"] != null)
	$basepath .= $models["topnavbar_category"]->getData()->url_name . "/";


$basepathnavbar = $basepath;

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
	t("Description") => $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/",
	t("Report duplicate") => $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/report_duplicate/",
	t("Help promote this idea!") => $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/promote/");

if(user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole) || $data["choice"]->userid == $GLOBALS['user']->uid)
	$menu[t("Edit")] = $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit/";

echo outputTabbedMenu($menu, 1); 
?>




<table style="border-spacing: 0px 0px; width: 100%; margin-top:0px; margin-left:6px">
<tr><td style="padding-top:0; color:rgb(100,100,100); font-size:11px; vertical-align:top">


<?php 
	echo t('Written by !name the !date at !time.',
		array("!name" => '<a class="authorlink" href="' . $basepath . "contributor/" . $data["choice"]->username  . '/">' .
			$data["choice"]->username . '</a>',
			"!date" => date('j M y',strtotime($data["choice"]->date)),
			"!time" => date('H:i',strtotime($data["choice"]->date)) ));
?> 



<?php
	//Show the global category if a relation is not selected
	if($data["choice"]->catname != null && $data["choice"]->relation_id == -1)
		echo t("Category: !cat.", array("!cat" => $data["choice"]->catname));
?>



<?php echo t('Related project:'); ?> <span id="relation_text">
<?php 
if($data["choice"]->relation_id != -1) 
	echo "<a href=\"" . $GLOBALS['basemodule_url'] . "/" . $data["choice"]->relation_url_name . "/\">" . 
		$data["choice"]->relation_name . "</a>"; 
else 
	echo t("Nothing/Others"); 
?>
</span>.


<?php if(UserModel::currentUserHasPermission("edit_relation", "Choice", $this->_model)) : ?>
<select name="relation" onchange="saveRelation(<?php echo $data["choice"]->id; ?>)" id="list_relations" style="display:none">
</select>

<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit/"; ?>" onclick="showRelationEdit(); return false;"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" title="<?php echo t('Edit the related project'); ?>" id="edit_relation_button"></a>
<?php endif; ?>






<?php
//Show the relation subcategory name if a relation is linked to this idea.
if($data["choice"]->relation_id != -1 && $data["choice"]->relationsubcatname != null)
	echo t("Category: !cat.", array("!cat" => $data["choice"]->relationsubcatname));
?>




<?php echo t('Status:'); ?> <span style="font-weight:bold<?php if(user_access($site->getData()->adminrole)) echo ";display:none"; ?>" id="status_string"><?php echo getStatusString($data["choice"]->status, $data["choice"]->bugstatus, $data["choice"]->specstatus, $data["choice"]->specdelivery); ?></span>

<?php 
	//Need edit_status perm. If we are in awaiting moderation, you'll need a special status to change the status.
	if(UserModel::currentUserHasPermission("edit_status", "Choice", $this->_model) &&
	($data["choice"]->status != ChoiceModel::$choice_status["awaiting_moderation"] || 
		UserModel::currentUserHasPermission("edit_status_in_awaiting_moderation", "Choice", $this->_model))) : ?>

<form method="post" action="" style="display:inline">

<select name="status" onchange="saveStatus(<?php echo $data["choice"]->id; ?>)" id="list_status">
	<option value="-2" <?php if($data["choice"]->status == -2) echo "selected=\"selected\" " ?>><?php echo t('Deleted'); ?></option>
	<option value="8" <?php if($data["choice"]->status == 8) echo "selected=\"selected\" " ?>><?php echo t('Awaiting moderation'); ?></option>
	<option value="0" <?php if($data["choice"]->status == -1 || $data["choice"]->status == 0) echo "selected=\"selected\" " ?>>New</option>
	<option value="1" <?php if($data["choice"]->status == 1) echo "selected=\"selected\" " ?>><?php echo t('Needs clarification'); ?></option>
	<option value="6" <?php if($data["choice"]->status == 6) echo "selected=\"selected\" " ?>><?php echo t('Blueprint approved'); ?></option>
	<option value="2" <?php if($data["choice"]->status == 2) echo "selected=\"selected\" " ?>><?php echo t('In development'); ?></option>
	<option value="3" <?php if($data["choice"]->status == 3) echo "selected=\"selected\" " ?>><?php echo t('Implemented'); ?></option>
	<option value="5" <?php if($data["choice"]->status == 5) echo "selected=\"selected\" " ?>><?php echo t('Already implemented'); ?></option>
	<option value="4" <?php if($data["choice"]->status == 4) echo "selected=\"selected\" " ?>><?php echo t("Won't implement"); ?></option>
	<option value="7" <?php if($data["choice"]->status == 7) echo "selected=\"selected\" " ?>><?php echo t('Not an idea (e.g. bug)'); ?></option>
</select>

<input type="hidden" name="_status_submitted" value="true" />
<input type="submit" value="<?php echo t('Save'); ?>" id="status_button" style="width:50px" />
</form>



<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit"; ?>" onclick="showStatusEdit(); return false;"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" alt="edit" title="<?php echo t('Change the status'); ?>" style="display:none" id="edit_status_button"></a>
<?php endif; ?>


<br />

<?php if(user_access($GLOBALS['site']->getData()->userrole)) : ?>

	<?php if($data["choice"]->status != ChoiceModel::$choice_status["workinprogress"] &&
		$data["choice"]->status != ChoiceModel::$choice_status["done"] &&
		$data["choice"]->status != ChoiceModel::$choice_status["not_an_idea"] &&
		$data["choice"]->status != ChoiceModel::$choice_status["already_done"] &&
		$data["choice"]->duplicatenumber == -1) : ?>
	<?php echo t('Report as <a href="!link">spam</a> / <a href="!link2">not an idea</a> / <a href="!link3">in development</a> / <a href="!link4">implemented</a>',
			array("!link" => $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/report_spam_idea/",
				"!link2" => $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/report_not_idea/", 
				"!link3" => $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/report_in_dev_idea/",
				"!link4" => $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/report_implemented_idea/" 
				));
	?>
	<?php elseif($data["choice"]->status == ChoiceModel::$choice_status["workinprogress"]) : ?>
	<?php echo t('Report as <a href="!link4">implemented</a>',
			array("!link4" => $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/report_implemented_idea/" 
				));
	?>
	<?php endif; ?>
	
<?php endif; ?>




<?php if(UserModel::currentUserHasPermission("edit_target_release", "Choice", $this->_model)) : ?>



<?php echo t('Target release (admin only):'); ?> <span style="font-weight:bold<?php if(user_access($site->getData()->adminrole)) echo ";display:none"; ?>" id="release_string"><?php if($data["choice"]->target_release_name != null) echo $data["choice"]->target_release_name; else echo "None"; ?></span>

<form method="post" action="" style="display:inline">


<select name="release" onchange="saveRelease(<?php echo $data["choice"]->id; ?>)" id="list_release">
	<option value="-1" <?php if($data["choice"]->release_target == -1) echo "selected=\"selected\" " ?>><?php echo t('None'); ?></option>
<?php if(count($data["releaselist"]) > 0) : ?>
<?php foreach($data["releaselist"] as $release) : ?>
	<option value="<?php echo $release->id; ?>" <?php if($data["choice"]->release_target == $release->id) echo "selected=\"selected\" " ?>><?php echo $release->long_name; ?></option>
<?php endforeach; ?>
<?php endif; ?>
</select>

<input type="hidden" name="_release_submitted" value="true" />
<input type="submit" value="Save" id="release_button" style="width:50px" />

</form>

<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit"; ?>" onclick="showReleaseEdit(); return false;"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" alt="edit" title="Change the Release" style="display:none" id="edit_release_button"></a>

<?php endif; ?>


<?php if(UserModel::currentUserHasPermission("mark_dup", "Choice", $models["choice"]) && $data["choice"]->duplicatenumber == -1) : ?>
<div class="dupballoon">
<div>
<form method="post" action="<?php echo $GLOBALS['basemodule_url'] . "/edit/"; ?>">
<b>Mark as duplicate of idea #</b> <input type="text" name="duplicate_of" style="width:50px"><input type="submit" style="width:30px; margin-left:5px" value="OK">
<input type="hidden" name="choice-id" value="<?php echo $data["choice"]->id; ?>" />
<input type="hidden" name="destination" value="<?php echo $GLOBALS['basemodule_path'] ?>" />
<input type="hidden" name="_choice_edited" value="true" />
</form>
</div><a rel="external" href="#">[<?php echo t('Mark as duplicate'); ?>]</a></div>
<?php endif; ?>








</td><td style="padding: 0px 0px 0px 0px; width:1%; vertical-align:top; text-align:right; white-space:nowrap">




<?php if(user_access($site->getData()->userrole)) : ?>
<?php if($data["choice"]->mybookmark == "t") : ?>
<a onclick="togglebookmark(<?php echo $data["choice"]->id; ?>); return false;" href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $data["choice"]->id; ?>/unbookmark/"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/bookmark.png" title="<?php echo t('Remove the bookmark'); ?>" id="bookmarkimg-<?php echo $data["choice"]->id; ?>"></a>
<?php else : ?>
<a onclick="togglebookmark(<?php echo $data["choice"]->id; ?>); return false;" href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $data["choice"]->id; ?>/bookmark/"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/bookmark-0.png" title="<?php echo t('Bookmark this idea'); ?>" id="bookmarkimg-<?php echo $data["choice"]->id; ?>"></a>
<?php endif; ?>
<?php endif; ?>
</td></tr>
<tr><td>




<div class= "qapoll_title2-2">
<?php echo t('Rationale'); ?>
<?php if(user_access($site->getData()->userrole) && $data["choice"]->userid == $GLOBALS['user']->uid || user_access($site->getData()->moderatorrole) || user_access($site->getData()->adminrole) || UserModel::currentUserHasPermission("edit_description", "Choice", $models["choice"])) : ?>	
<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit"; ?>"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" alt="edit" title="<?php echo t('Edit the description'); ?>"></a>
<?php endif; ?>
</div>

<div style="line-height:140%" <?php if($data["choice"]->duplicatenumber != -1) : ?>class="duplicate_description"<?php endif; ?>>
<?php 
if($data["choice"]->description != null)
	echo str_replace("\n", "<br />", force_text_wrap(linkify_URLS(strip_tags_and_evil_attributes($data["choice"]->description, $this->getThemeSetting("item_description_auth_tags")))));
else
	echo "[" . t('No description') . "]";
?>

<div style="padding-top:3px">
<?php echo t('Tags'); ?>:

<span id="tagtext" <?php if(user_access($site->getData()->userrole)) echo "style=\"display:none\""; ?>>
<?php if(count($data["choice"]->tags) == 0) : ?>
<span style="color:rgb(100,100,100)">(<?php echo t('none'); ?>)</span>
<?php else : ?>
<?php foreach($data["choice"]->tags as $tag) : ?>
	<a href="<?php echo $basepath; ?>?tags=<?php echo $tag->name; ?>"><?php echo $tag->name; ?></a>
<?php endforeach; ?>
<?php endif; ?>
</span>

<?php if(user_access($site->getData()->userrole)) : ?>
<form method="post" action="" id="formposttags" style="display:inline">
<input type="text" name="tags" value="<?php foreach($data["choice"]->tags as $tag) echo $tag->name . " "; ?>" id="tagstextbox" onKeyPress="return submitTags(event, <?php echo $data["choice"]->id ?>, 0, '<?php echo $basepath; ?>')" onblur="sendTags(<?php echo $data["choice"]->id ?>, 0, this.value, '<?php echo $basepath; ?>')">
<input type="hidden" name="_tags_submitted" value="true" />
<input type="submit" value="OK" style="margin-left:1px; width:40px" id="posttags">
</form>
<?php endif; ?>

<?php if(user_access($site->getData()->userrole)) : ?>
<a href="#" onclick="showTagsEdit(); return false;"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" id="edittags" style="display:none" title="<?php echo t('Edit the tags'); ?>"></a>
<?php endif; ?>



<?php if(user_access($site->getData()->adminrole) || user_access($site->getData()->developerrole)) : ?>

<?php echo t('Admin tags'); ?>:

<span id="admintagtext" style="display:none">
<?php if(count($data["choice"]->admintags) == 0) : ?>
<span style="color:rgb(100,100,100)">(<?php echo t('none'); ?>)</span>
<?php else : ?>
<?php foreach($data["choice"]->admintags as $tag) : ?>
	<a href="<?php echo $basepath; ?>?admintags=<?php echo $tag->name; ?>"><?php echo $tag->name; ?></a>
<?php endforeach; ?>
<?php endif; ?>
</span>

<form method="post" action="" id="formpostadmintags" style="display:inline">
<input type="text" name="admintags" value="<?php foreach($data["choice"]->admintags as $tag) echo $tag->name . " "; ?>" id="admintagstextbox" onKeyPress="return submitTags(event, <?php echo $data["choice"]->id ?>, 1, '<?php echo $basepath; ?>')" onblur="sendTags(<?php echo $data["choice"]->id ?>, 1, this.value, '<?php echo $basepath; ?>')">
<input type="hidden" name="_admintags_submitted" value="true" />
<input type="submit" value="OK" style="margin-left:1px; width:40px" id="postadmintags">
</form>

<a href="#" onclick="showAdminTagsEdit(); return false;"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" id="editadmintags" style="display:none" title="<?php echo t('Edit the admin tags'); ?>"></a>

<?php endif; ?>


</div>



</div>


</td>
<td style="vertical-align:top; width:1%">

<?php if($data["choice"]->status == ChoiceModel::$choice_status["awaiting_moderation"]) : ?>

<?php
	//Put a approval vote box here.
	$item_data["item"] =& $data["choice"];
	echo $this->loadTemplate("common/", "approvalbox", "", $item_data);
?>


<?php endif; ?>

</td>


</tr>
<tr>
<td colspan="2">


<br />


<?php if($data["choice"]->whiteboard != null || UserModel::currentUserHasPermission("edit_dev_comments", "Choice", $this->_model)) : ?>
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
<?php if(UserModel::currentUserHasPermission("edit_dev_comments", "Choice", $this->_model)) : ?>	
<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit"; ?>"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" alt="edit" title="<?php echo t('Edit the developer comments'); ?>"></a>
<?php endif; ?>	
</div>
<div style="font-weight:bold">
<?php echo str_replace("\n", "<br />",linkify_URLS(strip_tags_and_evil_attributes($data["choice"]->whiteboard, $this->getThemeSetting("item_description_auth_tags")))); ?>
</div>


</div>
<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>
<div style="margin-bottom:2px"></div>

<br />



<?php endif; ?>





<table class="choicelisting" style="width: 100%">
<?php if(count($data["choice"]->solutions) == 0) : ?>

<tr><td style="text-align:center">
<span style="font-weight:bold"><?php echo t('No solutions.'); ?></span>
</td></tr>

<?php else : ?>

<?php for ($i = 0; $i < count($data["choice"]->solutions); $i++) : ?>

<?php
	//Prepare the data and call the item solution template.
	$item_data = array();
	$item_data["item"] =& $data["choice"];
	$item_data["item_solution"] =& $data["choice"]->solutions[$i];
	$item_data["item_solution_pos"] = $i;
	$item_models["item"] = $models["choice"];
	$item_models["item_solutionlist"] = $models["choice"]->additional_models["choicesolutionlist"];
	echo $this->loadTemplate("choicelist/", "default", "item_solution", $item_data, $item_models);
?>

<?php endfor; ?>

<?php endif; ?>

</table>

<br />

<?php if($data["choice"]->status != ChoiceModel::$choice_status["workinprogress"] &&
		$data["choice"]->status != ChoiceModel::$choice_status["done"]) : ?>

<?php if(user_access($site->getData()->userrole)) : ?>	
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; ?>/add_solution" onclick="showHideSolutionArea(); return false;" class="undecoredlink" id="postsolutionlink" style="display:none"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/treeExpanded.png" alt="expand" title="<?php echo t('Post a solution'); ?>"> <?php echo t('Propose your solution'); ?></a>
<?php else : ?>
<a href="<?php echo $GLOBALS['base_url'] ?>/user?destination=<?php echo $GLOBALS['basemodule_path']; ?>/" class="undecoredlink"><?php echo t('Propose your solution'); ?></a>
<?php endif; ?>

<?php if(user_access($site->getData()->userrole)) : ?>
<form method="post" action="" id="postsolution">

<div class= "qapoll_title2" id="postsolutiontitle">
<?php echo t('Post your solution'); ?>
</div>

<span style="font-size:1.2em; font-weight:bold"><?php echo t('Solution title'); ?></span>
<br />
<input type="text" name="solution_title" maxlength="80" style="margin-top:2px; width:400px" value="<?php echo htmlentities($_POST["solution_title"], ENT_QUOTES, "UTF-8"); ?>">
<br />
<br />
<span style="font-size:1.2em; font-weight:bold"><?php echo t('Solution description'); ?></span>
<br />
<textarea cols="60" name="solution_text" rows="15" onKeyPress="limitText(this,5000);" style="margin-top:2px"><?php echo htmlentities($_POST["solution_text"], ENT_QUOTES, "UTF-8"); ?></textarea>
<input type="hidden" name="_solution_submitted" value="true" />
<br />
<br />
<input type="submit" value="Propose your solution" style="width:150px" />
</form>
<?php endif; ?>

<br />
<br />



<?php endif; ?>

















<?php if($this->getThemeSetting("show_choice_attachments")) : ?>

<div class= "qapoll_title2">
<?php echo t('Attachments'); ?>
</div>



<?php if($data["choice"]->specid != null) : ?>

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 
<div class="ubuntu_roundnavbar_main" style="padding-left:5px">

<table width="100%"><tr><td>
<a href="<?php echo htmlspecialchars($data["choice"]->specid); ?>" class="undecoredlink"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/spec.png" alt="spec"/> <?php echo t('Blueprint'); ?> <?php echo substr($data["choice"]->specid, strrpos($data["choice"]->specid, "/") + 1); ?>:</a> <?php echo ($data["choice"]->spectitle != null)?strip_tags_and_evil_attributes($data["choice"]->spectitle):"[" . t('Information on this blueprint will be retrieved soon') . "]"; ?>

</td><td>
<div class="rightbox">
<?php if(user_access($site->getData()->userrole)) : ?>	
<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit"; ?>"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" alt="edit" title="<?php echo t('Edit the blueprint attachment'); ?>"></a>
<?php endif; ?>
</div>
</td></tr></table>


</div>
<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>
<div style="margin-bottom:2px"></div>

<?php endif; ?>

<?php if($data["choice"]->forumid != null) : ?>

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 
<div class="ubuntu_roundnavbar_main" style="padding-left:5px">

<table width="100%"><tr><td>
<a href="http://ubuntuforums.org/showthread.php?t=<?php echo $data["choice"]->forumid; ?>" class="undecoredlink"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/forum.png" alt="forum"/> <?php echo t('Ubuntuforums.org thread #!number', array("!number" => $data["choice"]->forumid)); ?></a>

</td><td>
<div class="rightbox">
<?php if(user_access($site->getData()->userrole)) : ?>	
<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit"; ?>"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/edit.png" alt="edit" title="<?php echo t('Edit the thread attachment'); ?>"></a>
<?php endif; ?>
</div>
</td></tr></table>


</div>
<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>
<div style="margin-bottom:2px"></div>

<?php endif; ?>

<?php if($data["choice"]->bugid == null && $data["choice"]->forumid == null && $data["choice"]->specid == null) : ?>

<div style="text-align:center">
<?php echo t('No attachments.'); ?>
</div>

<?php endif; ?>

<?php if(user_access($site->getData()->userrole)) : ?>	

<div style="text-align:center; margin-top:5px">
<?php if($data["choice"]->specid == null) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit"; ?>" class="undecoredlink">[<?php echo t('Attach a blueprint'); ?>]</a> 
<?php endif; ?>
<?php if($data["choice"]->forumid == null) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/edit"; ?>" class="undecoredlink">[<?php echo t('Attach a ubuntuforums.org thread'); ?>]</a>
<?php endif; ?>
</div>

<?php endif; ?>

<br />

<?php endif; ?>




<?php if(true) : ?>
<br />
<div class= "qapoll_title2">
<?php echo t('Duplicates'); ?>
</div>
<div>

<?php if(count($data["choice"]->duplicates_items) != 0) : ?>

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 
<div class="ubuntu_roundnavbar_main" style="padding-left:10px; padding-top:5px; padding-bottom:5px; padding-right:10px">

<?php for($i=0; $i < count($data["choice"]->duplicates_items); $i++) : ?>

<div>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/yes.png" alt="Accepted" title="<?php echo t('Accepted'); ?>">
<?php 
	echo t('<a href="!link">Idea #!number: !title</a> (!nbvotes votes)',
		array("!link" => $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->duplicates_items[$i]->id . "/",
			"!number" => $data["choice"]->duplicates_items[$i]->id,
			"!title" => force_text_wrap(strip_tags_and_evil_attributes($data["choice"]->duplicates_items[$i]->title), 30),
			"!nbvotes" => $data["choice"]->duplicates_items[$i]->votes
			))
?>
</div>

<?php endfor; ?>


</div>
<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>
<div style="margin-bottom:2px"></div>

<?php endif; ?>


<div style="margin-top:4px; text-align:center">
<?php if(user_access($site->getData()->userrole)) : ?>	
<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->id . "/report_duplicate"; ?>">[<?php echo t('Report a duplicate and merge its votes to this idea'); ?>]</a>
<?php else : ?>
<a href="<?php echo $GLOBALS['base_url'] ?>/user?destination=<?php echo $GLOBALS['basemodule_path']; ?>/report_duplicate/">[<?php echo t('Report a duplicate and merge its votes to this idea'); ?>]</a>
<?php endif; ?>
</div>

</div>
<br />

<?php endif; ?>

<br />


<div class= "qapoll_title2">
<?php echo t('Comments'); ?>
</div>


<?php if(count($data["choice"]->comment_items) == 0) : ?>

	<div style="text-align:center">
	<span><?php echo t('No comments.'); ?></span>
	</div>

<?php else : ?>

<?php for ($i = 0; $i < count($data["choice"]->comment_items); $i++) : ?>

<?php
	$comment_data = array();
	$comment_options = array();
	$comment_data["comment"] =& $data["choice"]->comment_items[$i];
	$comment_options["basepath"] = $basepath;
	echo $this->loadTemplate("choice/", "default", "comment", $comment_data, array(), $comment_options);
?>

<?php endfor; ?>

<?php endif; ?>


<br />

<?php if(user_access($site->getData()->userrole)) : ?>	
<a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path']; ?>/add_comment" onclick="showHideCommentArea(); return false;" class="undecoredlink" id="postcommentlink" style="display:none"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/treeExpanded.png" alt="expand" title="<?php echo t('Post a comment'); ?>"> <?php echo t('Post your comment'); ?></a>
<?php else : ?>
<a href="<?php echo $GLOBALS['base_url'] ?>/user?destination=<?php echo $GLOBALS['basemodule_path']; ?>/" class="undecoredlink">Post your comment</a>
<?php endif; ?>

<?php if(user_access($site->getData()->userrole)) : ?>
<form method="post" action="" id="postcomment">

<div class= "qapoll_title2" id="postcommenttitle">
<?php echo t('Post your comment'); ?>
</div>

<textarea cols="60" name="commennt_text" rows="15" onKeyPress="limitText(this,5000);" style="margin-top:5px"></textarea>
<input type="hidden" name="_comment_submitted" value="true" />
<br />
<br />
<input type="submit" value="<?php echo t('Post your comment'); ?>" style="width:150px" />
</form>
<?php endif; ?>

</td></tr>
</table>

</div>
<!-- QAPoll ends here -->

