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
$navigation_comboboxs_suffix = "";
$navigation_tabs_suffix = "";
$ordering_combobox_suffix = "";
$ordering_combobox_index = 0;

$basepath = "";

$description = "";

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


//According to the ordering of the view, we choose the ordering combobox item to show, and the URL suffix to use on the relation/cat dropdownlist.
switch($models["choicelist"]->_ordering)
{
	case "new":
		$ordering_combobox_index = 0;
		$navigation_comboboxs_suffix = "latest_ideas/";		
		$description = 
			t("Here are the latest ideas about !project that have been approved. ", 
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	break;

	case "mosthype-day":
		$ordering_combobox_index = 1;
		$navigation_comboboxs_suffix = "most_popular_today/";	
		$description = 
			t("Here are today's most popular ideas about !project. ",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));	
	break;

	case "mosthype-week":
		$ordering_combobox_index = 2;
		$navigation_comboboxs_suffix = "most_popular_this_week/";
		$description = 
			t("Here are this week's most popular ideas about !project. ",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	break;

	case "mosthype-month":
		$ordering_combobox_index = 3;
		$navigation_comboboxs_suffix = "";
		$description =
			t("Here are this month's most popular ideas about !project. ",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	break;

	case "mosthype-6-months":
		$ordering_combobox_index = 4;
		$navigation_comboboxs_suffix = "most_popular_6_months/";
		$description =
			t("Here are the last 6 months most popular ideas about !project. ",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	break;

	case "mostvotes":
		$ordering_combobox_index = 5;
		$navigation_comboboxs_suffix = "most_popular_ever/";
		$description = 
			t("Here are the most popular ideas ever about !project. ",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	break;

	case "newcomments":
		$ordering_combobox_index = 6;
		$navigation_comboboxs_suffix = "latest_comments/";
		$description = 
			t("Here are the latest commented ideas about !project. ",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	break;

	case "random":
		$ordering_combobox_index = 7;
		$navigation_comboboxs_suffix = "random_ideas/";
		$description = 
			t("Here are random ideas about !project. ",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	break;
}


//Update the prefix if we are currently using a filter
$searchfilterarray = array();
if($_GET["keywords"] != null)
	$searchfilterarray["keywords"] = str_replace("\"", "", $_GET["keywords"]);
if($_GET["tags"] != null)
	$searchfilterarray["tags"] = str_replace("\"", "", $_GET["tags"]);
if($_GET["admintags"] != null)
	$searchfilterarray["admintags"] = str_replace("\"", "", $_GET["admintags"]);
$searchfilter = ((count($searchfilterarray) > 0)?"?" . generate_GET_param_list($searchfilterarray):"");
$navigation_comboboxs_suffix .= $searchfilter;
$ordering_combobox_suffix = $searchfilter;
$navigation_tabs_suffix = $searchfilter;

?>	

<!-- QAPoll starts here -->
<div class="qapoll">

<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = "popular";
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






<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 

<table width="100%" class="ubuntu_roundnavbar_main" ><tr>
<td style="padding-left:10px; width:1%">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/frontpage-thunder.png" alt="Popular ideas">
</td><td style="padding-left:10px">
<!--
<?php if($GLOBALS['entrypoint']->getData()->description != null) echo str_replace("\n", "<br />",strip_tags_and_evil_attributes($GLOBALS['entrypoint']->getData()->description, $this->getThemeSetting("entry_point_desc_auth_tags"))); else echo str_replace("\n", "<br />", $this->_data->description); ?>
-->
<?php echo $description; ?>
</td></tr></table>


<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>



<br />

<table style="width:100%">
<tr><td>

</td>
<td style="text-align:right;">



<div class="rightbox">




<?php
	//Put and configure the ordering dropdown list
	$dropdown_data["entries"] = array
		(
			t("Latest") => $basepath . "latest_ideas/" . $ordering_combobox_suffix,
			t("Most popular in 24 Hours") => $basepath . "most_popular_today/" . $ordering_combobox_suffix,
			t("Most popular in 7 days") => $basepath . "most_popular_this_week/" . $ordering_combobox_suffix,
			t("Most popular in 30 days") => $basepath . "" . $ordering_combobox_suffix,
			t("Most popular in 6 months") => $basepath . "most_popular_6_months/" . $ordering_combobox_suffix,
			t("Most popular ever") => $basepath . "most_popular_ever/" . $ordering_combobox_suffix,
			t("Latest comments") => $basepath . "latest_comments/" . $ordering_combobox_suffix,
			t("Random") => $basepath . "random_ideas/" . $ordering_combobox_suffix
		);
	$dropdown_data["selected_entry"] = $ordering_combobox_index;
	echo $this->loadTemplate("common/", "dropdownlist", "", $dropdown_data);
?>

</div>

</td></tr></table>





<table class="choicelisting" style="width: 100%;">

<?php if(count($this->_data->items) == 0) : ?>

<tr><td style="text-align:center">
<span style="font-weight:bold"><?php echo t('No entries.'); ?></span>
</td></tr>

<?php else : ?>

<?php for ($i = 0; $i < count($this->_data->items); $i++) : ?>

<?php
	$this->item =& $data["choicelist"]->items[$i];


	//Prepare the data and call the item template.
	$item_data = array();
	$item_models = array();
	$item_options = array();
	$item_data["item"] =& $data["choicelist"]->items[$i];
	$item_data["show_bottom_delimiter"] = (($i + 1 < count($data["choicelist"]->items))?"1":"0");
	$item_models["itemlist"] = $models["choicelist"];
	$item_models["item_solutionlist"] = $models["choicelist"]->additional_models["choicesolutionlist"][$item_data["item"]->id];
	$item_options["basepath"] = $basepath;
	echo $this->loadTemplate("choicelist/", "default", "item_v2", $item_data, $item_models, $item_options);
?>

<?php endfor; ?>

<?php endif; ?>

</table>

<div style="text-align:center">
<?php 

	//Prepare the data and call the pagination template.
	$pagination_data = array();
	$pagination_data["rowCount"] = $data["choicelist"]->rowCount;
	$pagination_data["page"] = $data["choicelist"]->page;
	$pagination_data["numberRowsPerPage"] = $data["choicelist"]->numberRowsPerPage;
	if($GLOBALS['basemodule_path'] != "")
		$pagination_data["url_prefix"] = $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/";
	else
		//Pagination for the default page. Use the list url.
		$pagination_data["url_prefix"] = $GLOBALS['basemodule_url'] . "/most_popular_this_month/";
	$get_params = generate_GET_param_list($_GET);
	$pagination_data["url_suffix"] = (($get_params != "")?"?" . $get_params:"");
	$pagination_data["url_middlefix"] = "/";

	echo $this->loadTemplate("common/", "pagination", "", $pagination_data);
?>
</div>

</div>
<!-- QAPoll ends here -->

