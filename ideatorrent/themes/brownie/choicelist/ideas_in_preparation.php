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
$navigation_subtabs_suffix = "";

$parentpath = "";
$basepath = "";
$basepath_plus_subtabpath = "";

$description = "";

$tabmenu_show_latest_ideas_submission_ordering = false;

//
// Lots of path computing before the theme
//

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

//Save the parent path
$parentpath = $basepath;
$basepath .= "ideas_in_preparation/";


//The necessary informations for the ordering combobox + the main navigation combobox
$navigation_comboboxs_suffix = "ideas_in_preparation/";
$basepath_plus_subtabpath = $basepath;


//Compute the prefix plus the idea_in_development path plus the release prefix.
if($models["choicelist"]->_filter_array["duplicate_items"] == -3)
{
	$navigation_subtabs_index = 3;
	$description = 
		t("Here are ideas about  !project that have been marked as duplicates by the moderators. ",
			array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
				$this->getThemeSetting("project_name"))));
}
else if($models["choicelist"]->_filter_array["state_awaiting_moderation"] != 0)
{
	$navigation_subtabs_index = 0;
	$tabmenu_show_latest_ideas_submission_ordering = true;
	$description = 
		t('Here are the newly submitted ideas about !project that are awaiting moderator validation before going to the "popular ideas" area.',
			array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
				$this->getThemeSetting("project_name"))));
}
else if($models["choicelist"]->_filter_array["state_not_an_idea"] != 0)
{
	$basepath_plus_subtabpath = $basepath . "invalid/";
	$navigation_comboboxs_suffix .= "invalid/";
	$navigation_subtabs_index = 1;
	$description = 
		t("Here are ideas about  !project that have been marked as not following the guidelines by the moderators.",
			array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
				$this->getThemeSetting("project_name"))));
}
else if($models["choicelist"]->_filter_array["state_already_done"] != 0)
{
	$basepath_plus_subtabpath = $basepath . "already_implemented/";
	$navigation_comboboxs_suffix .= "already_implemented/";
	$navigation_subtabs_index = 2;
	$description = 
		t("Here are ideas about  !project that have been marked as already implemented by the moderators.",
			array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
				$this->getThemeSetting("project_name"))));
}


//According to the ordering of the view, we choose the ordering combobox item to show, and the URL suffix to use on the relation/cat dropdownlist.
//Save the ordering prefix for the release menu.
$navigation_subtabs_suffix = "";
switch($models["choicelist"]->_ordering)
{
	case "latest-activity":
		$ordering_combobox_index = 0;	
	break;

	case "new":
		$ordering_combobox_index = 2;
	break;

	case "newcomments":
		$ordering_combobox_index = 1;
		$navigation_comboboxs_suffix .= "latest_comments/";
		$navigation_subtabs_suffix = "latest_comments/";
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
$navigation_subtabs_suffix .= $searchfilter;
$navigation_tabs_suffix = $searchfilter;
$ordering_combobox_suffix .= $searchfilter;

?>	

<!-- QAPoll starts here -->
<div class="qapoll">


<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = "ingestation";
	$navbar_options["biglinks_prefix"] = $parentpath;
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

<table width="100%" class="ubuntu_roundnavbar_main"><tr>
<td style="padding-left:10px; width:1%">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/frontpage-biohazard3.png" alt="Ideas in development">
</td><td style="padding-left:10px">

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
<tr><td style="padding-right:5px">
<?php
	//We prepare the tabbed menu.
	$menu = array();

	$menu[t("Valid candidates")] = $basepath . $navigation_subtabs_suffix;
	$menu[t("Invalid ideas")] = $basepath . "invalid/" . $navigation_subtabs_suffix;
	$menu[t("Already implemented")] = $basepath . "already_implemented/" . $navigation_subtabs_suffix;
	$menu[t("Duplicates")] = $basepath . "duplicates/" . $navigation_subtabs_suffix;

	$tabmenu_data["entries"] = $menu;
	$tabmenu_data["selected_entry"] = $navigation_subtabs_index;
	echo $this->loadTemplate("common/", "tabbedmenu", "", $tabmenu_data);
?>
</td>
<td style="text-align:right; width:1%">



<div class="rightbox">




<?php
	//Put and configure the ordering dropdown list
	$dropdown_data["entries"] = array();
	$dropdown_data["entries"][t("Latest activity")] = $basepath_plus_subtabpath . $ordering_combobox_suffix;
	$dropdown_data["entries"][t("Latest comments")] = $basepath_plus_subtabpath . "latest_comments/" . $ordering_combobox_suffix;
	if($tabmenu_show_latest_ideas_submission_ordering)
			$dropdown_data["entries"][t("Latest idea submissions")] = $basepath_plus_subtabpath . "latest_submissions/" . $ordering_combobox_suffix;

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
	$item_options["basepath"] = $parentpath;
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
	$pagination_data["url_prefix"] = $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/";
	$get_params = generate_GET_param_list($_GET);
	$pagination_data["url_suffix"] = (($get_params != "")?"?" . $get_params:"");
	$pagination_data["url_middlefix"] = "/";

	echo $this->loadTemplate("common/", "pagination", "", $pagination_data);
?>
</div>


</div>
<!-- QAPoll ends here -->
