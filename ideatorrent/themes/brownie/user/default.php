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

$navigation_comboboxs_suffix = "contributor/" . $data["user"]->name . "/";


//We will use the stats of the user. Load them
//TODO: Move that to an appropriate place
$stats = $this->_model->getStats();

//TEMP
//print_r($this->_model->getData());
//global $poll;
//$choicelist = new ChoiceListModel($poll);
//$choicelist->setFilterParameters(array("ordering" => "new"));
//echo $this->_model->hasPermission("edit_relation", "choice", $choicelist, 10201);

//$choice = new ChoiceModel();
//$choice->setId(3645);
//echo $this->_model->hasPermission("edit_relation", "choice", $choice);

//echo $this->_model->hasPermission("edit_frontpage_desc", "entrypoint", $GLOBALS['entrypoint']);

//Amarok dev
//$bpp = array("global" => array(), "Choice" => array("filtered_perms" => array("relation=21&all_states=1" => array("edit_status" => true, "edit_target_release" => true, "edit_dev_comments" => true, "edit_relation" => true, "approve_idea" => true, "select_solution" => true))), "EntryPoint" => array("filtered_perms" => array("entry_point_ids=4" => array("show_admin_page" => true, "edit_frontpage_desc" => true, "show_process_dup_page" => true))));
//print_r(serialize($bpp));
//echo "<br><br>";

//Moderator
//$bpp = array("global" => array("process_report" => true), "Choice" => array("edit_status" => true, "edit_target_release" => true, "edit_relation" => true, "approve_idea" => true, "delete_solution" => true, "mark_solution_dup" => true, "mark_dup" => true, "edit_solution" => true, "edit_title" => true, "edit_description" => true), "User" => array("delete_user_items" => true));
//print_r(serialize($bpp));
//echo "<br><br>";

//Idea reviewer
//$bpp = array("global" => array(), "Choice" => array("filtered_perms" => array("state_workinprogress=0&state_done=0&state_awaiting_moderation=1&duplicate_items=-2" => array("status_mark_as_nonidea" => true, "status_mark_as_already_implemented" => true, "edit_relation" => true, "approve_idea" => true, "edit_solution" => true, "edit_title" => true, "edit_description" => true))));
//print_r(serialize($bpp));
//echo "<br><br>";

//Ubuntu dev
//$bpp = array("global" => array(), "Choice" => array("edit_status" => true, "edit_target_release" => true, "edit_dev_comments" => true, "edit_relation" => true, "search_by_admintags" => true, "approve_idea" => true, "select_solution" => true));
//print_r(serialize($bpp));

//Normal user
//$bpp = array("global" => array(), "Choice" => array("owner_perms" => array("edit_relation" => true), "submit_idea" => true, "submit_solution" => true), "Menu" => array("show_my_dashboard_link" => true), "ChoiceSolution" => array("owner_perms" => array("edit_solution" => true)));
//print_r(serialize($bpp));


?>
	
<!-- QAPoll starts here -->
<div class="qapoll">



<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = "none";
	$navbar_options["biglinks_prefix"] = $basepath;
	$navbar_options["comboboxs_suffix"] = $navigation_comboboxs_suffix;
	if($GLOBALS['gbl_relation'] != null)
	{
		$navbar_models["selected_relation"] = $GLOBALS['gbl_relation'];
		if($GLOBALS['gbl_relationsubcat'] != null)
			$navbar_models["selected_relationsubcat"] = $GLOBALS['gbl_relationsubcat'];
	}
	if($GLOBALS['gbl_category'] != null)
		$navbar_models['selected_category'] = $GLOBALS['gbl_category'];
	echo $this->loadTemplate("common/", "navigationtopbar", "", $navbar_data, $navbar_models, $navbar_options);

?>



<br />	

<?php


$title="";
if($data["user"]->uid == $GLOBALS['user']->uid)
{
	if($GLOBALS['gbl_relation'] != null)
	{
		if($GLOBALS['gbl_relationsubcat'] != null)
			$title = t("My dashboard on !relation (!subcat)", 
				array("!relation" => $GLOBALS['gbl_relation']->getData()->name,
					"!subcat" => $GLOBALS['gbl_relationsubcat']->getData()->name));
		else
			$title = t("My dashboard on !relation", array("!relation" => $GLOBALS['gbl_relation']->getData()->name));

	}
	else if($GLOBALS['gbl_category'] != null)
		$title = t("My dashboard on the !category category", array("!category" => $GLOBALS['gbl_category']->getData()->name));
	else
		$title = t("My dashboard");
}
else
{
	if($GLOBALS['gbl_relation'] != null)
	{
		if($GLOBALS['gbl_relationsubcat'] != null)
			$title = t("Contributor !name on !relation (!subcat)", 
				array("!relation" => $GLOBALS['gbl_relation']->getData()->name,
					"!subcat" => $GLOBALS['gbl_relationsubcat']->getData()->name,
					"!name" => $data["user"]->name));
		else
			$title = t("Contributor !name on !relation", 
				array("!relation" => $GLOBALS['gbl_relation']->getData()->name, "!name" => $data["user"]->name));

	}
	else if($GLOBALS['gbl_category'] != null)
		$title = t("Contributor !name on the !category category", 
			array("!category" => $GLOBALS['gbl_category']->getData()->name, "!name" => $data["user"]->name));
	else
		$title = t("Contributor !name", array("!name" => $data["user"]->name));
}
/**
if($GLOBALS['gbl_relation'] != null)
{
	$title .= " on " . $GLOBALS['gbl_relation']->getData()->name;
	if($GLOBALS['gbl_relationsubcat'] != null)
		$title .= " (" . $GLOBALS['gbl_relationsubcat']->getData()->name . ")";
}
if($GLOBALS['gbl_category'] != null)
	$title .= " on the " . $GLOBALS['gbl_category']->getData()->name . " category";
*/

?>

<div style="font-size:25px; padding-left:10px">
<?php echo $title; ?>
</div>

<br />

<div style="padding:1px">

<?php
	//We prepare the tabbed menu.
	$menu = array();
	$selected_menu_entry = -1;

	$menu["Summary"] = $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/";
	$menu[(($this->_data->uid == $GLOBALS['user']->uid)?t("My ideas"):t("Ideas"))] =
		$GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/ideas/";
	$menu[(($this->_data->uid == $GLOBALS['user']->uid)?t("My solutions"):t("Solutions"))] =
		$GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/solutions/";
	$menu[(($this->_data->uid == $GLOBALS['user']->uid)?t("Ideas I promoted"):t("Ideas promoted"))] =
		$GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/ideas_promoted/";
	$menu[(($this->_data->uid == $GLOBALS['user']->uid)?t("Ideas I demoted"):t("Ideas demoted"))] =
		$GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/ideas_demoted/";
	$menu[(($this->_data->uid == $GLOBALS['user']->uid)?t("Ideas I commented"):t("Ideas commented"))] = 
		$GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/ideas_commented/";
	$menu[(($this->_data->uid == $GLOBALS['user']->uid)?t("My bookmarks"):t("Ideas bookmarked"))] =
		$GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/ideas_bookmarked/";

	echo outputTabbedMenu($menu, 1);
?>


<br />
<br />

<div style="text-align:center; font-size:18px">

<?php if($this->_data->uid == $GLOBALS['user']->uid) : ?>
<?php echo t('How others users rated my solutions'); ?>
<?php else : ?>
<?php echo t("How others users rated !name's solutions", array("!name" => $data["user"]->name)); ?>
<?php endif; ?><br />


<div style="padding-bottom:20px; display:inline">

<?php echo $stats->nb_minus_vote_on_user_ideas; ?> 
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/down.png" alt="down"> 
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/overall_idea_quality.png.php?plus=<?php echo $stats->nb_plus_vote_on_user_ideas; ?>&minus=<?php echo $stats->nb_minus_vote_on_user_ideas; ?>"><?php
	echo $stats->nb_plus_vote_on_user_ideas; ?> 
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/up.png" alt="up">

</div>
</div>



<br />



<div class="qapoll_title2" style="padding-bottom:0px">
<?php if($this->_data->uid == $GLOBALS['user']->uid) : ?>
<?php echo t('My global stats'); ?>
<?php else : ?>
<?php echo t("!name's global stats", array("!name" => $data["user"]->name)); ?>
<?php endif; ?>
</div>

<div style="font-size:15px;">
<ul style="margin-top:5px">
<li style="padding-bottom:5px">
<?php if($stats->idea_quality_rank == null) : ?>

<?php echo t('No solution contribution yet.'); ?>

<?php else: ?>

<?php
if($stats->idea_quality_rank == 1)
	echo t("<b>THE</b> best solution contributor");
elseif($stats->idea_quality_rank%10 == 1 && $stats->idea_quality_rank != 11)
{
       echo t("<b>!rankst</b> best solution contributor", array("!rank" => $stats->idea_quality_rank));
}
elseif($stats->idea_quality_rank%10 == 2 && $stats->idea_quality_rank != 12)
{
       echo t("<b>!ranknd</b> best solution contributor", array("!rank" => $stats->idea_quality_rank));
}
elseif($stats->idea_quality_rank%10 == 3 && $stats->idea_quality_rank != 13)
{
       echo t("<b>!rankrd</b> best solution contributor", array("!rank" => $stats->idea_quality_rank));
}
else
{
	echo t("<b>!rankth</b> best solution contributor", array("!rank" => $stats->idea_quality_rank));
}
?>
<?php
if($this->_data->uid == $GLOBALS['user']->uid)
	echo t(" (overall score of your solutions: <b>!solscore</b>)", 
		array("!solscore" => $stats->nb_plus_vote_on_user_ideas - $stats->nb_minus_vote_on_user_ideas));
else
	echo t(" (overall score of his solutions: <b>!solscore</b>)", 
		array("!solscore" => $stats->nb_plus_vote_on_user_ideas - $stats->nb_minus_vote_on_user_ideas));
?>


<?php endif; ?>
</li>
<li style="padding-bottom:5px">
<?php 
	echo t("!voteup !imgvoteup and !votedown !imgvotedown votes cast", 
		array("!voteup" => "<b>" . $stats->nb_plus_vote_user_casted . "</b>",
			"!imgvoteup" => '<img src="/' . $this->getThemePath() . '/images/up.png" alt="up">',
			"!votedown" => "<b>" . $stats->nb_minus_vote_user_casted . "</b>",
			"!imgvotedown" => '<img src="/' . $this->getThemePath() . '/images/down.png" alt="down">'
			)); 
?>

</li> 
<li>
<?php 
	echo t("!nbcomments comments", 
		array("!nbcomments" => "<b>" . $stats->nb_comments_of_user . "</b>")); 
?>
</li>
</ul>

</div>




<?php if(UserModel::currentUserHasPermission("delete_user_items", "User", $models["user"])) : ?>

<div class="qapoll_title2" style="padding-bottom:0px">
<?php echo t('Admin tools'); ?>
</div>
<ul style="font-size:15px; margin-top:5px">
<li>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; ?>/delete_user_ideas/"><?php echo t('Delete ALL his/her ideas'); ?></a>
</li>
<li>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; ?>/delete_user_solutions/"><?php echo t('Delete ALL his/her solutions'); ?></a>
</li>
<li>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; ?>/delete_user_comments/"><?php echo t('Delete ALL his/her comments'); ?></a>
</li>
</ul>
<br />
<?php endif; ?>



<?php if(user_access($site->getData()->userrole) && $this->_data->uid != $GLOBALS['user']->uid && 
	qawebsite_get_user_setting("I can receive private messages", $this->_data->uid, QAWebsiteSite::getInstance()->id, "ideatorrent") !== "0") : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/contact_user"; ?>" style="display:none; font-size:15px" id="contactuserlink" onclick="showHideSendMsgArea(); return false;"><img src="<?php echo "/" . $this->getThemePath(); ?>/images/treeExpanded.png" alt="expand" title="Contact this user"> <?php echo t('Contact !name', array("!name" => $data["user"]->name)); ?></a>

<div class="qapoll_title2" style="padding-bottom:0px" id="contactusertitle">
<?php echo t('Contact !name', array("!name" => $data["user"]->name)); ?>
</div>

<form method="post" action="" id="contactuser">
<?php echo t('Write a message to !name. Your email address will be attached so that he can directly reply to you.', array("!name" => $data["user"]->name)); ?>

<div>
<textarea cols="60" name="message_text" rows="15" onKeyPress="limitText(this,5000);" style="margin-top:5px"></textarea>
</div>
<input type="hidden" name="_message_submitted" value="true" />
<br />
<input type="submit" value="Send your message" style="width:150px" />
</form>

<?php elseif(user_access($site->getData()->userrole) && $this->_data->uid == $GLOBALS['user']->uid) : ?>

<div class="qapoll_title2" style="padding-bottom:0px">
<?php echo t('My preferences'); ?>
</div>
<div style="font-size:15px;">
<form method="post" action="">
<?php if(qawebsite_get_user_setting("I can receive private messages", $GLOBALS['user']->uid, QAWebsiteSite::getInstance()->id, "ideatorrent") === "0") : ?>
<?php echo t('Nobody can send me private messages.'); ?>&nbsp;<input type="submit" value="<?php echo t('Allow'); ?>" style="width:80px" />
<input type="hidden" name="_allow_private_msg" value="true" />
<?php else: ?>
<?php echo t('Any registered user can send me a private message'); ?>.&nbsp;<input type="submit" value="<?php echo t('Forbid'); ?>" style="width:80px" />
<input type="hidden" name="_forbid_private_msg" value="true" />
<?php endif; ?>

</form>
</div>

<?php endif; ?>


</div>



</div>
<!-- QAPoll ends here -->

