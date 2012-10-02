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

//Compute if there is at least one selected solution
$at_least_one_selected_choice = false;
if($data["item"]->status == ChoiceModel::$choice_status["workinprogress"] ||
	$data["item"]->status == ChoiceModel::$choice_status["done"])
	foreach($data["item"]->solutions as $solution)
		if($solution->selected)
			$at_least_one_selected_choice = true;



?>



<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 

<table class="ubuntu_roundnavbar_main" style="padding:5px; line-height:90%; text-align: center;">
<tr><td colspan="3">
<span style="font-size:22px"><b id="votingnumber-<?php echo $data["item_solution"]->id; ?>"><?php echo $data["item_solution"]->total_votes; ?></b></span>
<br />
<span style="font-size:10px"><?php echo t('votes'); ?></span>
</td></tr>


<tr><td colspan="3" style="padding-top:0px; padding-bottom:0px; padding-left:5px">
<span style="display:none" id="voteupcount-<?php echo $data["item_solution"]->id; ?>"><?php echo $data["item_solution"]->total_plus_votes; ?></span>
<span style="display:none" id="voteequalcount-<?php echo $data["item_solution"]->id; ?>"><?php echo $data["item_solution"]->total_equal_votes; ?></span>
<span style="display:none" id="votedowncount-<?php echo $data["item_solution"]->id; ?>"><?php echo $data["item_solution"]->total_minus_votes; ?></span>


<div style="background:#808080; height:6px; width:60px" title="<?php 
	echo t("!promotions promotions / !dontcare don't care / !demotions demotions",
		array("!promotions" => $data["item_solution"]->total_plus_votes,
			"!dontcare" => $data["item_solution"]->total_equal_votes,
			"!demotions" => $data["item_solution"]->total_minus_votes));
?>" id="votebar-<?php echo $data["item_solution"]->id; ?>">


<table style="width:auto">
<tr>
<td style="background:#11f605; height:6px; width:<?php 
	if($data["item_solution"]->total_plus_votes + $data["item_solution"]->total_equal_votes + $data["item_solution"]->total_minus_votes > 0)
		echo round(60*($data["item_solution"]->total_plus_votes / ($data["item_solution"]->total_plus_votes +
			$data["item_solution"]->total_equal_votes + $data["item_solution"]->total_minus_votes)));
	else
		echo 0;
?>px; padding:0px" id="upbar-<?php echo $data["item_solution"]->id; ?>">
</td>
<td style="background:#ffa327; height:6px; width:<?php 
	if($data["item_solution"]->total_plus_votes + $data["item_solution"]->total_equal_votes + $data["item_solution"]->total_minus_votes > 0)
		echo round(60*($data["item_solution"]->total_equal_votes / ($data["item_solution"]->total_plus_votes + 
			$data["item_solution"]->total_equal_votes + $data["item_solution"]->total_minus_votes)));
		else
			echo 0;
?>px; padding:0px" id="equalbar-<?php echo $data["item_solution"]->id; ?>">
</td>
<td style="background:#f00900; height:6px; width:<?php
	if($data["item_solution"]->total_plus_votes + $data["item_solution"]->total_equal_votes + $data["item_solution"]->total_minus_votes > 0)
		echo round(60*($data["item_solution"]->total_minus_votes / ($data["item_solution"]->total_plus_votes +
			 $data["item_solution"]->total_equal_votes + $data["item_solution"]->total_minus_votes))); 
		else
			echo 0;
?>px; padding:0px" id="downbar-<?php echo $data["item_solution"]->id; ?>">
</td>

</tr>
</table>

</div>

</td></tr>

<?php 
	//Show the voting arrow only if we are in the NEW, NEED_INFOS and BLUEPRINT_APPROVED state, and if NOT a duplicate
	if(($data["item"]->status == ChoiceModel::$choice_status["new"] ||
		$data["item"]->status == ChoiceModel::$choice_status["old_new"] || 
		$data["item"]->status == ChoiceModel::$choice_status["needinfos"] ||
		$data["item"]->status == ChoiceModel::$choice_status["blueprint_approved"]) 
		&& $data["item"]->duplicatenumber == -1) : 
?>

<tr><td style="padding-right:0px">

<?php if(!user_access($GLOBALS['site']->getData()->userrole)) : ?>
<a href="<?php echo $GLOBALS['base_url'] ?>/user?destination=ideatorrent/<?php echo $GLOBALS['basemodule_path']; ?>" id="linkvoteup-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/up22.png" alt="up" title="<?php echo t('Promote this solution!'); ?>" id="votingimageup-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php elseif($data["item_solution"]->myvote == -2) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/1" onclick="voteUp(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvoteup-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/up22.png" alt="up" title="<?php echo t('Promote this solution!'); ?>" id="votingimageup-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php elseif($data["item_solution"]->myvote != -2 && $data["item_solution"]->myvote != 1) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/1" onclick="voteUp(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvoteup-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/up22-0.png" alt="up" title="<?php echo t('Promote this solution!'); ?>" id="votingimageup-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php else : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/up22.png" alt="up" title="<?php echo t('You promoted this solution'); ?>" id="votingimageup-<?php echo $data["item_solution"]->id; ?>">
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/1" onclick="voteUp(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvoteup-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php endif; ?>


</td><td style="padding-left:2px; padding-right:2px">

<?php if(!user_access($GLOBALS['site']->getData()->userrole)) : ?>
<a href="<?php echo $GLOBALS['base_url'] ?>/user?destination=ideatorrent/<?php echo $GLOBALS['basemodule_path']; ?>" id="linkvoteequal-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/equal20.png" alt="equal" title="<?php echo t("Don't care"); ?>" id="votingimageequal-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php elseif($data["item_solution"]->myvote == -2) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/0" onclick="voteEqual(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvoteequal-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/equal20.png" alt="equal" title="<?php echo t("Don't care"); ?>" id="votingimageequal-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php elseif($data["item_solution"]->myvote != -2 && $data["item_solution"]->myvote != 0) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/0" onclick="voteEqual(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvoteequal-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/equal20-0.png" alt="equal" title="<?php echo t("Don't care"); ?>" id="votingimageequal-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php else : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/equal20.png" alt="equal" title="<?php echo t("You cast a blank vote"); ?>" id="votingimageequal-<?php echo $data["item_solution"]->id; ?>">
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/0" onclick="voteEqual(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvoteequal-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php endif; ?>



</td><td style="padding-left:0px">

<?php if(!user_access($GLOBALS['site']->getData()->userrole)) : ?>
<a href="<?php echo $GLOBALS['base_url'] ?>/user?destination=ideatorrent/<?php echo $GLOBALS['basemodule_path']; ?>" id="linkvotedown-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/down22.png" alt="down" title="<?php echo t("Demote this solution!"); ?>" id="votingimagedown-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php elseif($data["item_solution"]->myvote == -2) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/-1" onclick="voteDown(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvotedown-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/down22.png" alt="down" title="<?php echo t("Demote this solution!"); ?>" id="votingimagedown-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php elseif($data["item_solution"]->myvote != -2 && $data["item_solution"]->myvote != -1) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/-1" onclick="voteDown(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvotedown-<?php echo $data["item_solution"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/down22-0.png" alt="down" title="<?php echo t("Demote this solution!"); ?>" id="votingimagedown-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php else : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/down22.png" alt="down" title="<?php echo t("You demoted this solution"); ?>" id="votingimagedown-<?php echo $data["item_solution"]->id; ?>">
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>vote/<?php echo $data["item_solution"]->id; ?>/-1" onclick="voteDown(<?php echo $data["item_solution"]->id; ?>); return false;" id="linkvotedown-<?php echo $data["item_solution"]->id; ?>">
</a>
<?php endif; ?>

</td></tr>

<?php elseif($data["item"]->duplicatenumber != -1) : ?>

<tr><td>
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="closed" title="<?php echo t('Duplicate idea'); ?>">
</td></tr>

<?php elseif($data["item"]->status == ChoiceModel::$choice_status["workinprogress"]) : ?>

	<?php
		//Put the in dev icon only if the solution is selected, or if no solution is selected 
		if($at_least_one_selected_choice == true && $data["item_solution"]->selected == true ||
			$at_least_one_selected_choice == false) :
	?>

	<tr><td>
		<img src="<?php echo "/" . $this->getThemePath(); ?>/images/inprogress.png" alt="inprogress" title="<?php echo t('Solution in development'); ?>" >
	</td></tr>
	<?php else : ?>

	<tr><td>
		<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="closed" title="<?php echo t('This solution was not selected'); ?>">
	</td></tr>

	<?php endif; ?>

<?php elseif($data["item"]->status == ChoiceModel::$choice_status["done"]) : ?>

	<?php
		//Put the in dev icon only if the solution is selected, or if no solution is selected 
		if($at_least_one_selected_choice == true && $data["item_solution"]->selected == true ||
			$at_least_one_selected_choice == false) :
	?>

	<tr><td>
		<img src="<?php echo "/" . $this->getThemePath(); ?>/images/implemented.png" alt="implemented" title="<?php echo t('Implemented Solution'); ?>" >
	</td></tr>
	<?php else : ?>

	<tr><td>
		<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="closed" title="<?php echo t('This solution was not selected'); ?>">
	</td></tr>

	<?php endif; ?>

<?php elseif($data["item"]->status == ChoiceModel::$choice_status["already_done"]) : ?>

<tr><td>
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="closed" title="<?php echo t('Idea already done'); ?>">
</td></tr>

<?php elseif($data["item"]->status == ChoiceModel::$choice_status["unapplicable"]) : ?>

<tr><td>
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="closed" title="<?php echo t("Won't Implement"); ?>">
</td></tr>

<?php elseif($data["item"]->status == ChoiceModel::$choice_status["deleted"]) : ?>

<tr><td>
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="closed" title="<?php echo t('Deleted idea'); ?>">
</td></tr>

<?php elseif($data["item"]->status == ChoiceModel::$choice_status["not_an_idea"]) : ?>

<tr><td>
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="closed" title="<?php echo t('Invalid idea'); ?>">
</td></tr>

<?php elseif($data["item"]->status == ChoiceModel::$choice_status["awaiting_moderation"]) : ?>

<tr><td>
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="closed" title="<?php echo t('Voting is not allowed in the idea sandbox'); ?>">
</td></tr>

<?php endif; ?>



</table>

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>


