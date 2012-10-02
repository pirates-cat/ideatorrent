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

//Ok, we want to show the item to be marked as duplicate on the left.
if(strtotime($data["item"]->origdate) > strtotime($data["item"]->dupdate))
{
	$left = "orig";

	$leftid = $data["item"]->choiceid;
	$lefttitle = $data["item"]->origtitle;
	$leftdate = $data["item"]->origdate;
	$leftsolutions = $data["item"]->solutionsorig;
	$leftdescription = $data["item"]->origdescription;

	$rightid = $data["item"]->duplicateid;
	$righttitle = $data["item"]->duptitle;
	$rightdate = $data["item"]->dupdate;
	$rightsolutions = $data["item"]->solutionsdup;
	$rightdescription = $data["item"]->dupdescription;
}
else
{
	$left = "dup";

	$rightid = $data["item"]->choiceid;
	$righttitle = $data["item"]->origtitle;
	$rightdate = $data["item"]->origdate;
	$rightsolutions = $data["item"]->solutionsorig;
	$rightdescription = $data["item"]->origdescription;

	$leftid = $data["item"]->duplicateid;
	$lefttitle = $data["item"]->duptitle;
	$leftdate = $data["item"]->dupdate;
	$leftsolutions = $data["item"]->solutionsdup;
	$leftdescription = $data["item"]->dupdescription;
}

?>	


<tr id="dupreprow1-<?php echo $data["item"]->id; ?>"><td colspan="3">

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 

<table width="100%" class="ubuntu_roundnavbar_main">
<tr><td style="padding-left:5px; width:50%">
<a class="blacklink" href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $leftid; ?>/"><span style="font-weight:bold; font-size:large"><?php echo force_text_wrap(strip_tags_and_evil_attributes($lefttitle), 30); ?></a> </span>
<br />
<span style="font-weight:bold;">(<?php echo t('submitted the !date', array("!date" => date('j M y',strtotime($leftdate)))); ?>)</span>
</td><td>
<a class="blacklink" href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $rightid; ?>/"><span style="font-weight:bold; font-size:large"><?php echo force_text_wrap(strip_tags_and_evil_attributes($righttitle), 30); ?></a> </span>
<br />
<span style="font-weight:bold;">(<?php echo t('submitted the !date', array("!date" => date('j M y',strtotime($rightdate)))); ?>)</span>
</td></tr></table>


<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>


</td></tr>



<tr id="dupreprow2-<?php echo $data["item"]->id; ?>"><td style="vertical-align:top; width:40%">


<div style="padding-top:5px; padding-bottom:5px; text-align:center">


<a href="<?php echo $GLOBALS['basemodule_url'] . "/process_duplicate_reports/mark_as_duplicate_" . $left . "/" . $data["item"]->id . "/"; ?>" class="undecoredlink" onclick="mark_as_duplicate_<?php echo $left; ?>(<?php echo $data["item"]->id; ?>); return false;">[<?php echo t('Mark as duplicate'); ?>] </a>


</div>


<?php 
if($leftdescription != null)
	echo str_replace("\n", "<br />", limit_number_of_lines(force_text_wrap(strip_tags_and_evil_attributes($leftdescription, $this->getThemeSetting("item_description_auth_tags")), 50)));
else
	echo "[" . t("No description") . "]";
?>

<br />
<br />

<?php if(count($leftsolutions) == 0) : ?>

<div style="text-align:center">
<span style="font-weight:bold"><?php echo t('No solutions.'); ?></span>
</div>

<?php else : ?>

<?php for ($i = 0; $i < count($leftsolutions); $i++) : ?>

<?php
	//Prepare the data and call the item solution template.
	$item_data = array();
	$item_data["leftid"] = $leftid; 
	$item_data["rightid"] = $rightid;
	$item_data["item"] =& $data["item"];
	$item_data["item_solution"] =& $leftsolutions[$i];
	$item_data["other_item_solutions"] =& $rightsolutions;
	$item_data["show_admin_links"] = 1;

	//Check if this solution is already linked to the right solution. If yes, we should not show the admin links.
	for ($j = 0; $j < count($rightsolutions); $j++)
	{
		if($rightsolutions[$j]->id == $leftsolutions[$i]->id)
			$item_data["show_admin_links"] = 0;
	}

	echo $this->loadTemplate("duplicate_reportlist/", "default", "item_solution", $item_data);
?>

<?php endfor; ?>

<?php endif; ?>



</td><td style="width:10%; padding:5px 10px 10px 10px; text-align:center; vertical-align:top">


<a href="<?php echo $GLOBALS['basemodule_url'] . "/process_duplicate_reports/discard_duplicate_report/" . $data["item"]->id . "/"; ?>" class="undecoredlink" onclick="discard_duplicate_report(<?php echo $data["item"]->id; ?>); return false;"><?php echo t('Discard report'); ?></a><br />
<br />
<span style="font-size:x-small">
<?php 
	echo t('By <a href="!link" class="authorlink">!name</a> the !date',
		array("!link" => $GLOBALS['basemodule_url'] . "/contributor/" . $data["item"]->submittername . "/",
			"!name" => $data["item"]->submittername,
			"!date" => date('j M y',strtotime($data["item"]->date))
		));
?>

</span>

</td><td style="vertical-align:top" id="dupreprow3-<?php echo $data["item"]->id; ?>"">

<div style="padding-top:5px; padding-bottom:5px; text-align:center">



</div>



<?php 
if($rightdescription != null)
	echo str_replace("\n", "<br />", limit_number_of_lines(force_text_wrap(strip_tags_and_evil_attributes($rightdescription, $this->getThemeSetting("item_description_auth_tags")), 50)));
else
	echo "[" . t("No description") . "]";
?>

<br />
<br />

<?php if(count($rightsolutions) == 0) : ?>

<div style="text-align:center">
<span style="font-weight:bold"><?php echo t('No solutions.'); ?></span>
</div>

<?php else : ?>

<?php for ($i = 0; $i < count($rightsolutions); $i++) : ?>

<?php
	//Prepare the data and call the item solution template.
	$item_data = array();
	$item_data["item"] =& $data["item"];
	$item_data["item_solution"] =& $rightsolutions[$i];
	$item_data["other_item_solutions"] =& $leftsolutions;
	$item_data["show_admin_links"] = 0;

	echo $this->loadTemplate("duplicate_reportlist/", "default", "item_solution", $item_data);
?>

<?php endfor; ?>

<?php endif; ?>


</td></tr>

<tr id="dupreprow4-<?php echo $data["item"]->id; ?>"><td colspan="3">
&nbsp;
</td></tr>

