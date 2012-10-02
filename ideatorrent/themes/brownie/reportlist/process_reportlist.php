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

//Declaration of all the variables
$title = "";
$basepath = "";
$navigation_subtabs_prefix = "";
$navigation_subtabs_index = 0;
$navigation_subtabs_menu = array();
$process_choice_string = "";
$process_choice_string_2 = "";
$process_comment_string = "";
$process_solution_string = "";



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


//Select the title, and a few others things based on the type of items we are processing
if($models["reportlist"]->_filter_array["report_type"] == ReportModel::$type['spam'])
{
	$title = "Process the spam reports";
	$navigation_subtabs_prefix = $basepath . "process_spam_reports/";
	$navigation_subtabs_menu["Solutions"] = $navigation_subtabs_prefix . "";
	$navigation_subtabs_menu["Comments"] = $navigation_subtabs_prefix . "comments/";
	$navigation_subtabs_menu["Ideas"] = $navigation_subtabs_prefix . "ideas/";

	//Compute the subtabs index
	if($models["reportlist"]->_filter_array["item_type"] == ReportModel::$item_type['solution'])
		$navigation_subtabs_index = 0;
	else if($models["reportlist"]->_filter_array["item_type"] == ReportModel::$item_type['comment'])
		$navigation_subtabs_index = 1;
	else if($models["reportlist"]->_filter_array["item_type"] == ReportModel::$item_type['choice'])
		$navigation_subtabs_index = 2;

	$process_choice_string = t("Delete idea");
	$process_comment_string = t("Delete comment");
	$process_solution_string = t("Delete solution");
}
else if($models["reportlist"]->_filter_array["report_type"] == ReportModel::$type['offensive'])
{
	$title = "Process the offensive reports";
	$navigation_subtabs_prefix = $basepath . "process_offensive_reports/";

	$process_comment_string = t("Delete comment");
}
else if($models["reportlist"]->_filter_array["report_type"] == ReportModel::$type['indev'])
{
	$title = "Process the in development reports";
	$navigation_subtabs_prefix = $basepath . "process_indev_reports/";

	$process_choice_string = t("Mark as being in development");
}
else if($models["reportlist"]->_filter_array["report_type"] == ReportModel::$type['implemented'])
{
	$title = "Process the implemented reports";
	$navigation_subtabs_prefix = $basepath . "process_implemented_reports/";

	$process_choice_string = t("Mark as implemented");
	$process_choice_string_2 = t("Mark as already implemented");
}
if($models["reportlist"]->_filter_array["report_type"] == ReportModel::$type['not_an_idea'])
{
	$title = "Process the irrelevance reports";
	$navigation_subtabs_prefix = $basepath . "process_irrelevance_reports/";
	$navigation_subtabs_menu["Solutions"] = $navigation_subtabs_prefix . "";
	$navigation_subtabs_menu["Ideas"] = $navigation_subtabs_prefix . "ideas/";

	//Compute the subtabs index
	if($models["reportlist"]->_filter_array["item_type"] == ReportModel::$item_type['solution'])
		$navigation_subtabs_index = 0;
	else if($models["reportlist"]->_filter_array["item_type"] == ReportModel::$item_type['choice'])
		$navigation_subtabs_index = 1;

	$process_choice_string = t("Mark as &quot;not an idea&quot;");
	$process_solution_string = t("Delete solution");
}


?>

<!-- QAPoll right block starts here -->
<div class="qapoll">


<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = "";
	$navbar_options["biglinks_prefix"] = $basepath;
	$navbar_options["biglinks_suffix"] = $navigation_tabs_suffix;
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

<b class="ubuntu_title">
<b class="ubuntu_title1"><b></b></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title5"></b></b>


<table width="100%" class="ubuntu_title_main"><tr><td>
<h1 style="padding:10px 0px 0px 10px; margin: 0px 0px 0px 0px">
<?php echo $title; ?>
</h1><br />

</td></tr></table>


<b class="ubuntu_title">
<b class="ubuntu_title5"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title1"><b></b></b></b>

<br />

<?php
	//We prepare the tabbed menu.
	$tabmenu_data["entries"] = $navigation_subtabs_menu;
	$tabmenu_data["selected_entry"] = $navigation_subtabs_index;
	echo $this->loadTemplate("common/", "tabbedmenu", "", $tabmenu_data);
?>

<br />

<?php if(count($data["reportlist"]->items) == 0) : ?>
<br />
<div style="text-align:center">
<span style="font-weight:bold"><?php echo t('No reports... yet!'); ?></span>
</div>

<?php else : ?>

<?php for ($i = 0; $i < count($data["reportlist"]->items); $i++) : ?>


<div id="report-<?php echo $data["reportlist"]->items[$i]->id; ?>">


<?php if($data["reportlist"]->items[$i]->item_type == ReportModel::$item_type["solution"]) : ?>



	<b class="ubuntu_roundnavbar">
	<b class="ubuntu_roundnavbar1"><b></b></b>
	<b class="ubuntu_roundnavbar2"><b></b></b>
	<b class="ubuntu_roundnavbar3"></b>
	<b class="ubuntu_roundnavbar4"></b>
	<b class="ubuntu_roundnavbar5"></b></b> 

	<table class="ubuntu_roundnavbar_main" style="text-align:center; width:100%">
	<tr>
	<td>

	<?php foreach($data["reportlist"]->items[$i]->choicelistmodel->getData()->items as $linked_idea) :  ?>
	<?php echo t('A solution of <a href="!link">Idea #!number: !title</a>', 
		array("!link" => $GLOBALS["basemodule_url"] . "/idea/" . $linked_idea->id . "/",
			"!number" => $linked_idea->id,
			"!title" => force_text_wrap(strip_tags_and_evil_attributes($linked_idea->title), 30)
			));
	?>
	<br />
	<?php endforeach; ?>

	<form style="display:inline" method="get" action="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/accept_report/" . $data["reportlist"]->items[$i]->id; ?>">
	<input type="submit" value="<?php echo $process_solution_string; ?>" onclick="accept_report(<?php echo $data["reportlist"]->items[$i]->id; ?>); return false">
	</form>
	<form style="display:inline" method="get" action="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/discard_report/" . $data["reportlist"]->items[$i]->id; ?>" onclick="discard_report(<?php echo $data["reportlist"]->items[$i]->id; ?>); return false">
	<input type="submit" value="Discard report">
	</form>

	</td>
	<td style="width:1%; white-space:nowrap; font-size:16px; padding-right:5px">
	<span style="font-size:20px"><?php echo $data["reportlist"]->items[$i]->votes; ?></span> <?php echo t('reports'); ?>
	</td>
	</tr>
	</table>

	<b class="ubuntu_roundnavbar">
	<b class="ubuntu_roundnavbar5"></b>
	<b class="ubuntu_roundnavbar4"></b>
	<b class="ubuntu_roundnavbar3"></b>
	<b class="ubuntu_roundnavbar2"><b></b></b>
	<b class="ubuntu_roundnavbar1"><b></b></b></b>

	<br />

	<table>

	<?php
		//Prepare the data and call the item solution template.
		$item_data = array();
		$item_data["item_solution"] =& $data["reportlist"]->items[$i]->model->getData();
		$item_data["show_report_links"] = -1;
		echo $this->loadTemplate("choicelist/", "default", "item_solution", $item_data, $item_models);
	?>
	</table>

	<br />
	<br />


<?php elseif($data["reportlist"]->items[$i]->item_type == ReportModel::$item_type["comment"]) : ?>






	<b class="ubuntu_roundnavbar">
	<b class="ubuntu_roundnavbar1"><b></b></b>
	<b class="ubuntu_roundnavbar2"><b></b></b>
	<b class="ubuntu_roundnavbar3"></b>
	<b class="ubuntu_roundnavbar4"></b>
	<b class="ubuntu_roundnavbar5"></b></b> 

	<table class="ubuntu_roundnavbar_main" style="text-align:center; width:100%">
	<tr>
	<td>
	<?php echo t('A comment of <a href="!link">Idea #!number: !title</a>', 
		array("!link" => $GLOBALS["basemodule_url"] . "/idea/" . $data["reportlist"]->items[$i]->choicemodel->getData()->id . "/",
			"!number" => $data["reportlist"]->items[$i]->choicemodel->getData()->id,
			"!title" => force_text_wrap(strip_tags_and_evil_attributes($data["reportlist"]->items[$i]->choicemodel->getData()->title), 30)
			));
	?>
	<br />

	<form style="display:inline" method="get" action="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/accept_report/" . $data["reportlist"]->items[$i]->id; ?>">
	<input type="submit" value="<?php echo $process_comment_string; ?>" onclick="accept_report(<?php echo $data["reportlist"]->items[$i]->id; ?>); return false">
	</form>
	<form style="display:inline" method="get" action="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/discard_report/" . $data["reportlist"]->items[$i]->id; ?>">
	<input type="submit" value="Discard report" onclick="discard_report(<?php echo $data["reportlist"]->items[$i]->id; ?>); return false">
	</form>

	</td>
	<td style="width:1%; white-space:nowrap; font-size:16px; padding-right:5px">
	<span style="font-size:20px"><?php echo $data["reportlist"]->items[$i]->votes; ?></span> <?php echo t('reports'); ?>
	</td>
	</tr>
	</table>

	<b class="ubuntu_roundnavbar">
	<b class="ubuntu_roundnavbar5"></b>
	<b class="ubuntu_roundnavbar4"></b>
	<b class="ubuntu_roundnavbar3"></b>
	<b class="ubuntu_roundnavbar2"><b></b></b>
	<b class="ubuntu_roundnavbar1"><b></b></b></b>

	<br />

	<?php

	$comment_data = array();
	$comment_data["comment"] =& $data["reportlist"]->items[$i]->model->getData();
	$comment_data["show_report_links"] = -1;
	$comment_data["show_admin_links"] = -1;
	echo $this->loadTemplate("choice/", "default", "comment", $comment_data);

	?>

	<br />
	<br />


<?php elseif($data["reportlist"]->items[$i]->item_type == ReportModel::$item_type["choice"]) : ?>


	<b class="ubuntu_roundnavbar">
	<b class="ubuntu_roundnavbar1"><b></b></b>
	<b class="ubuntu_roundnavbar2"><b></b></b>
	<b class="ubuntu_roundnavbar3"></b>
	<b class="ubuntu_roundnavbar4"></b>
	<b class="ubuntu_roundnavbar5"></b></b> 

	<table class="ubuntu_roundnavbar_main" style="text-align:center; width:100%">
	<tr>
	<td>

	<form style="display:inline" method="get" action="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/accept_report/" . $data["reportlist"]->items[$i]->id; ?>">
	<input type="submit" value="<?php echo $process_choice_string; ?>" onclick="accept_report(<?php echo $data["reportlist"]->items[$i]->id; ?>); return false">
	</form>

	<?php if($process_choice_string_2 != "") : ?>
	<form style="display:inline" method="get" action="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/accept_report2/" . $data["reportlist"]->items[$i]->id; ?>">
	<input type="submit" value="<?php echo $process_choice_string_2; ?>" onclick="accept_report2(<?php echo $data["reportlist"]->items[$i]->id; ?>); return false">
	</form>
	<?php endif; ?>

	<form style="display:inline" method="get" action="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/discard_report/" . $data["reportlist"]->items[$i]->id; ?>">
	<input type="submit" value="Discard report" onclick="discard_report(<?php echo $data["reportlist"]->items[$i]->id; ?>); return false">
	</form>

	</td>
	<td style="width:1%; white-space:nowrap; font-size:16px; padding-right:5px">
	<span style="font-size:20px"><?php echo $data["reportlist"]->items[$i]->votes; ?></span> <?php echo t('reports'); ?>
	</td>
	</tr>
	</table>

	<b class="ubuntu_roundnavbar">
	<b class="ubuntu_roundnavbar5"></b>
	<b class="ubuntu_roundnavbar4"></b>
	<b class="ubuntu_roundnavbar3"></b>
	<b class="ubuntu_roundnavbar2"><b></b></b>
	<b class="ubuntu_roundnavbar1"><b></b></b></b>


	<br />

	<table>
	<?php

		//Prepare the data and call the item template.
		$item_data = array();
		$item_data["item"] =& $data["reportlist"]->items[$i]->model->getData();
		$item_models["itemlist"] = $data["reportlist"]->items[$i]->model;
//		$item_models["item_solutionlist"] = $models["choicelist"]->additional_models["choicesolutionlist"][$item_data["item"]->id];
		$item_data["show_report_links"] = -1;
		$item_data["show_admin_links"] = -1;
		echo $this->loadTemplate("choicelist/", "default", "item_v2", $item_data, $item_models);
	?>
	</table>

	<br />
	<br />


<?php endif; ?>

</div>


<?php endfor; ?>

<?php endif; ?>


<div style="text-align:center">
<?php 

	//Prepare the data and call the pagination template.
	$pagination_data = array();
	$pagination_data["rowCount"] = $data["reportlist"]->rowCount;
	$pagination_data["page"] = $data["reportlist"]->page;
	$pagination_data["numberRowsPerPage"] = $data["reportlist"]->numberRowsPerPage;
	$pagination_data["url_prefix"] = $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/";
	$get_params = generate_GET_param_list($_GET);
	$pagination_data["url_suffix"] = (($get_params != "")?"?" . $get_params:"");
	$pagination_data["url_middlefix"] = "/";

	echo $this->loadTemplate("common/", "pagination", "", $pagination_data);
?>
</div>

</div>
<!-- QAPoll right block ends here -->
