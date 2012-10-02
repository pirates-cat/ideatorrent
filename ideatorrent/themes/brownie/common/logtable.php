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

//The choice that is used.
$filter = "choice";

if($models["choiceloglist"]->_filter_array["user_id"] != null)
	$filter = "user";

?>

<table style="width:100%">
<tr style="border: 1px solid grey; text-align:center" class="duptableheader">
<td style="border: 1px solid grey">
	<?php echo t('Date'); ?>
</td>
<td style="border: 1px solid grey">
	<?php
		if($filter == "choice")
			echo t('Who');
		else
			echo t('Where');
	?>
</td>
<td style="border: 1px solid grey">
	<?php echo t('What changed'); ?>
</td>
<td style="border: 1px solid grey">
	<?php echo t('Old value'); ?>
</td>
<td style="border: 1px solid grey">
	<?php echo t('New value'); ?>
</td>
</tr>

<?php for($i = 0; $i < count($data["choiceloglist"]); $i++) : ?>


<?php 
	//Do not show admin tags info if the user is not developer or admin
	if($data["choiceloglist"][$i]->change != ChoiceLogModel::$change["admintags"] || user_access($site->getData()->adminrole) || user_access($site->getData()->developerrole)) :
?>


<?php if($i + 1 == count($data["choiceloglist"])) : ?>
	<tr style="border-bottom: 1px solid grey">
<?php else : ?>
	<tr style="border-bottom: 1px dotted rgb(210, 210, 210)">
<?php endif; ?>

<td style="padding:5px; vertical-align:top">
	<?php echo date('j M y H:i',strtotime($data["choiceloglist"][$i]->date)); ?>
</td>

<td style="padding:5px; vertical-align:top">
	<?php if($filter == "choice") : ?>
	<a href="<?php echo $GLOBALS['basemodule_url'] . $GLOBALS['basemodule_prefilter_path']; ?>/contributor/<?php echo $data["choiceloglist"][$i]->username; ?>/"><?php echo $data["choiceloglist"][$i]->username; ?></a>
	<?php else : ?>
		<?php echo t('<a href="!link">Idea #!number</a>', 
			array("!link" => $GLOBALS['basemodule_url'] . "/idea/" . $data["choiceloglist"][$i]->choiceid . "/",
				"!number" =>$data["choiceloglist"][$i]->choiceid
				));
		?>
	<?php endif; ?>
</td>

<td style="padding:5px; vertical-align:top">

<?php
	if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["title"])
		echo t("Rationale title");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["description"])
		echo t("Rationale description");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["status"])
		echo t("Status");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["category"])
		echo t("Global category");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["relation"])
		echo t("Related project");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["relationsubcat"])
		echo t("Project category");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["target_release"])
		echo t("Target release");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["duplicate"])
		echo t("Duplicate link");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["tags"])
		echo t("Tags");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["admintags"])
		echo t("Admin tags");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_linked"])
	{
		if($data["choiceloglist"][$i]->new_value== $data["choice"]->id)
			//Solution linked in this choice
			echo t("Solutions");
		else
			//Solution linked in another choice
			echo t("Solution #!number links", array("!number" => $data["choiceloglist"][$i]->solutionlink_model->getData()->solution_number));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_unlinked"])
	{
		if($data["choiceloglist"][$i]->old_value== $data["choice"]->id)
			//Solution linked in this choice
			echo t("Solutions");
		else
			//Solution linked in another choice
			echo t("Solution #!number links", array("!number" => $data["choiceloglist"][$i]->solutionlink_model->getData()->solution_number));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["comment_added"])
		echo t("Comment added");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["comment_deleted"])
		echo t("Comment deleted");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["whiteboard"])
		echo t("Developer comments");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["moderatorapproval"])
		echo t("Moderator approval");
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_title"])
		echo t("Solution #!number title", array("!number" => $data["choiceloglist"][$i]->solutionlink_model->getData()->solution_number));
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_description"])
		echo t("Solution #!number description", array("!number" => $data["choiceloglist"][$i]->solutionlink_model->getData()->solution_number));
?>

</td>

<td style="padding:5px; vertical-align:top">
<?php
	if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["title"])
	{
		echo htmlentities($data["choiceloglist"][$i]->old_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["description"])
	{
		echo str_replace("\n", "<br />", htmlentities($data["choiceloglist"][$i]->old_value));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["status"])
	{
		echo $data["choiceloglist"][$i]->old_status_name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["category"])
	{
		if($data["choiceloglist"][$i]->old_value == -1)
			echo t("Others");
		else
			echo $data["choiceloglist"][$i]->old_category_model->getData()->name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["relation"])
	{
		if($data["choiceloglist"][$i]->old_value == -1)
			echo t("Nothing/Others");
		else
			echo $data["choiceloglist"][$i]->old_relation_model->getData()->name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["relationsubcat"])
	{
		if($data["choiceloglist"][$i]->old_value == -1)
			echo t("Others");
		else
			echo $data["choiceloglist"][$i]->old_relationsubcat_model->getData()->name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["target_release"])
	{
		if($data["choiceloglist"][$i]->old_value == -1)
			echo t("None");
		else
			echo $data["choiceloglist"][$i]->old_release_model->getData()->long_name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["duplicate"])
	{
		if($data["choiceloglist"][$i]->old_value == -1)
			echo t("Not a duplicate");
		else
			echo t('<a href="!link">Idea #!number: !title</a>',
				array("!link" => $basepath . "idea/" . $data["choiceloglist"][$i]->old_value . "/",
					"!number" => $data["choiceloglist"][$i]->old_value,
					"!title" => strip_tags_and_evil_attributes($data["choiceloglist"][$i]->old_duplicate_model->getData()->title)));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["tags"])
	{
		echo htmlentities($data["choiceloglist"][$i]->old_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["admintags"])
	{
		echo htmlentities($data["choiceloglist"][$i]->old_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_linked"])
	{
		if($data["choiceloglist"][$i]->new_value== $data["choice"]->id)
		{
			//Solution linked to this choice.
		}
		else
		{
			//Solution linked in another choice.
		}
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_unlinked"])
	{
		if($data["choiceloglist"][$i]->old_value== $data["choice"]->id)
		{
			//Solution linked to this choice.
			$sol_data = explode('<>', $data["choiceloglist"][$i]->old_value2);
			echo t('<b>Solution #!number: !title</b><br />!description',
				array("!number" => $data["choiceloglist"][$i]->solutionlink_model->getData()->solution_number,
					"!title" => htmlentities($sol_data[0]),
					"!description" => str_replace("\n", "<br />", 
						htmlentities($sol_data[1]))
				));
		}
		else
		{
			//Solution linked in another choice.
			echo t('Link to <a href="!link">idea #!number: !title</a>',
				array("!link" => $GLOBALS['basemodule_url'] . '/idea/' . $data["choiceloglist"][$i]->new_value . "/",
					"!number" => $data["choiceloglist"][$i]->new_value,
					"!title" => htmlentities($data["choiceloglist"][$i]->choice_model->getData()->title)
					));
		}
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["comment_added"])
	{

	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["comment_deleted"])
	{
		echo str_replace("\n", "<br />", htmlentities($data["choiceloglist"][$i]->old_value));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["whiteboard"])
	{
		echo str_replace("\n", "<br />", htmlentities($data["choiceloglist"][$i]->old_value));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["moderatorapproval"])
	{
		echo htmlentities($data["choiceloglist"][$i]->old_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_title"])
	{
		echo htmlentities($data["choiceloglist"][$i]->old_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_description"])
	{
		echo str_replace("\n", "<br />", htmlentities($data["choiceloglist"][$i]->old_value));
	}

?>
</td>

<td style="padding:5px; vertical-align:top">
<?php
	if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["title"])
	{
		echo htmlentities($data["choiceloglist"][$i]->new_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["description"])
	{
		echo str_replace("\n", "<br />", htmlentities($data["choiceloglist"][$i]->new_value));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["status"])
	{
		echo $data["choiceloglist"][$i]->new_status_name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["category"])
	{
		if($data["choiceloglist"][$i]->new_value == -1)
			echo t("Others");
		else
			echo $data["choiceloglist"][$i]->new_category_model->getData()->name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["relation"])
	{
		if($data["choiceloglist"][$i]->new_value == -1)
			echo t("Nothing/Others");
		else
			echo $data["choiceloglist"][$i]->new_relation_model->getData()->name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["relationsubcat"])
	{
		if($data["choiceloglist"][$i]->new_value == -1)
			echo t("Others");
		else
			echo $data["choiceloglist"][$i]->new_relationsubcat_model->getData()->name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["target_release"])
	{
		if($data["choiceloglist"][$i]->new_value == -1)
			echo t("None");
		else
			echo $data["choiceloglist"][$i]->new_release_model->getData()->long_name;
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["duplicate"])
	{
		if($data["choiceloglist"][$i]->new_value == -1)
			echo t("Not a duplicate");
		else
			echo t('<a href="!link">Idea #!number: !title</a>',
				array("!link" => $basepath . "idea/" . $data["choiceloglist"][$i]->new_value . "/",
					"!number" => $data["choiceloglist"][$i]->new_value,
					"!title" => strip_tags_and_evil_attributes($data["choiceloglist"][$i]->new_duplicate_model->getData()->title)));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["tags"])
	{
		echo htmlentities($data["choiceloglist"][$i]->new_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["admintags"])
	{
		echo htmlentities($data["choiceloglist"][$i]->new_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_linked"])
	{
		if($data["choiceloglist"][$i]->new_value== $data["choice"]->id)
		{
			//Solution linked to this choice.
			$sol_data = explode('<>', $data["choiceloglist"][$i]->new_value2);
			echo t('<b>Solution #!number: !title</b><br />!description',
				array("!number" => $data["choiceloglist"][$i]->solutionlink_model->getData()->solution_number,
					"!title" => htmlentities($sol_data[0]),
					"!description" => str_replace("\n", "<br />", 
						htmlentities($sol_data[1]))
				));
		}
		else
		{
			//Solution linked in another choice.
			echo t('New link to <a href="!link">idea #!number: !title</a>',
				array("!link" => $GLOBALS['basemodule_url'] . '/idea/' . $data["choiceloglist"][$i]->new_value . "/",
					"!number" => $data["choiceloglist"][$i]->new_value,
					"!title" => htmlentities($data["choiceloglist"][$i]->choice_model->getData()->title)
					));
		}
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_unlinked"])
	{
		if($data["choiceloglist"][$i]->old_value== $data["choice"]->id)
		{
			//echo '<span style="color:grey">' . t("deleted") . '</a>';
		}
		else
		{
			//Solution linked in another choice.
		}
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["comment_added"])
	{
		echo str_replace("\n", "<br />", htmlentities($data["choiceloglist"][$i]->new_value));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["comment_deleted"])
	{
		
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["whiteboard"])
	{
		echo str_replace("\n", "<br />", htmlentities($data["choiceloglist"][$i]->new_value));
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["moderatorapproval"])
	{
		echo htmlentities($data["choiceloglist"][$i]->new_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_title"])
	{
		echo htmlentities($data["choiceloglist"][$i]->new_value);
	}
	else if($data["choiceloglist"][$i]->change == ChoiceLogModel::$change["solution_description"])
	{
		echo str_replace("\n", "<br />", htmlentities($data["choiceloglist"][$i]->new_value));
	}

?>
</td>

</tr>

<?php endif; ?>
<?php endfor; ?>


</table>




