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
$basepath .= "ideas_in_development/";


//The necessary informations for the ordering combobox + the main navigation combobox
$ordering_combobox_index = 0;
$navigation_comboboxs_suffix = "ideas_in_development/";


//Compute the prefix plus the idea_in_development path plus the release prefix.
if($models["release"] == null)
{
	if(count($data["newreleaselist"]) > 0)
	{
		//We have some release in the DB. So if a release is not selected, we want to show ideas with no milestone.
		$basepath_plus_subtabpath = $basepath . "no_milestone/";
		$navigation_comboboxs_suffix .= "no_milestone/";
		$description = 
			t("Here are the ideas about !project that are currently being implemented with no milestone set.",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	}
	else
	{
		$basepath_plus_subtabpath = $basepath;
		$description = 
			t("Here are the ideas about !project that are currently being implemented.",
				array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name"))));
	}
}
else if($models["most_recent_release"]->getId() == $models["release"]->getId())
{
	$basepath_plus_subtabpath = $basepath;
	$description = 
		t("Here are the ideas about !project that are currently being implemented for the !release release.",
			array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name")),
				"!release" => "<b>" . $models["release"]->getData()->long_name . "</b>"));
}
else
{
	$basepath_plus_subtabpath = $basepath . $models["release"]->getData()->small_name . "/";
	$navigation_comboboxs_suffix .= $models["release"]->getData()->small_name . "/";
	$description = 
		t("Here are the ideas about !project that are currently being implemented for the !release release.",
			array("!project" => (($GLOBALS['gbl_relation'] != null)?"<b>" . $GLOBALS['gbl_relation']->getData()->name . "</b>":
					$this->getThemeSetting("project_name")),
				"!release" => "<b>" . $models["release"]->getData()->long_name . "</b>"));
}


//According to the ordering of the view, we choose the ordering combobox item to show, and the URL suffix to use on the relation/cat dropdownlist.
//Save the ordering prefix for the release menu.
switch($models["choicelist"]->_ordering)
{
	case "newstatuschange":
		$ordering_combobox_index = 0;	
	break;

	case "mostvotes":
		$ordering_combobox_index = 1;
		$navigation_comboboxs_suffix .= "most_popular/";		
		$navigation_subtabs_suffix = "most_popular/";
	break;

	case "newcomments":
		$ordering_combobox_index = 2;
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
	$navbar_options["selected_entry"] = "indev";
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
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/frontpage-inprogress.png" alt="Ideas in development">
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
	$selected_item = count($data["newreleaselist"]);
	$i = 0;

	foreach($data["newreleaselist"] as $release)
	{
		if($release->old_release == "f")
		{
			if($models["most_recent_release"]->getId() == $release->id)
				$menu[$release->long_name] = $basepath . $navigation_subtabs_suffix;
			else
				$menu[$release->long_name] = $basepath . $release->small_name . "/" . $navigation_subtabs_suffix;
			if($models["release"] != null && $release->id == $models["release"]->getId())
				$selected_item = $i;
			$i++;
		}
	}
	if(count($menu) > 0)
		$menu[t("No milestone")] = $basepath . "no_milestone/" . $navigation_subtabs_suffix;

	$tabmenu_data["entries"] = $menu;
	$tabmenu_data["selected_entry"] = $selected_item;
	echo $this->loadTemplate("common/", "tabbedmenu", "", $tabmenu_data);
?>
</td>
<td style="text-align:right; width:1%">



<div class="rightbox">




<?php
	//Put and configure the ordering dropdown list
	$dropdown_data["entries"] = array
		(
			t("Latest additions") => $basepath_plus_subtabpath . "" . $ordering_combobox_suffix,
			t("Most popular") => $basepath_plus_subtabpath . "most_popular/" . $ordering_combobox_suffix,
			t("Latest comments") => $basepath_plus_subtabpath . "latest_comments/" . $ordering_combobox_suffix,
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
