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
$navigation_subtabs_index = 0;

$parentpath = "";
$basepath = "";
$basepath_plus_subtabpath = "";

$tabmenu_show_my_latest_votes_ordering = false;

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
$basepath .= "contributor/" . $data["user"]->name . "/";


//The necessary informations for the ordering combobox + the main navigation combobox
$navigation_comboboxs_suffix = "contributor/" . $data["user"]->name . "/";
$basepath_plus_subtabpath = $basepath;


//Compute the prefix plus the idea_in_development path plus the release prefix.
if($models["choicelist"]->_filter_array["user"] != 0)
{
	$basepath_plus_subtabpath = $basepath . "ideas/";
	$navigation_comboboxs_suffix .= "ideas/";
	$navigation_subtabs_index = 1;
}
else if($models["choicelist"]->_filter_array["solution_userid"] != 0)
{
	$basepath_plus_subtabpath = $basepath . "solutions/";
	$navigation_comboboxs_suffix .= "solutions/";
	$navigation_subtabs_index = 2;
}
else if($models["choicelist"]->_filter_array["user_voted_items_vote_value"] == 1)
{
	$basepath_plus_subtabpath = $basepath . "ideas_promoted/";
	$navigation_comboboxs_suffix .= "ideas_promoted/";
	$navigation_subtabs_index = 3;
	$tabmenu_show_my_latest_votes_ordering = true;
}
else if($models["choicelist"]->_filter_array["user_voted_items_vote_value"] == -1)
{
	$basepath_plus_subtabpath = $basepath . "ideas_demoted/";
	$navigation_comboboxs_suffix .= "ideas_demoted/";
	$navigation_subtabs_index = 4;
	$tabmenu_show_my_latest_votes_ordering = true;
}
else if($models["choicelist"]->_filter_array["user_commented_items"] > -1)
{
	$basepath_plus_subtabpath = $basepath . "ideas_commented/";
	$navigation_comboboxs_suffix .= "ideas_commented/";
	$navigation_subtabs_index = 5;
}
else if($models["choicelist"]->_filter_array["user_bookmarked_items"] > -1)
{
	$basepath_plus_subtabpath = $basepath . "ideas_bookmarked/";
	$navigation_comboboxs_suffix .= "ideas_bookmarked/";
	$navigation_subtabs_index = 6;
}

//According to the ordering of the view, we choose the ordering combobox item to show, and the URL suffix to use on the relation/cat dropdownlist.
//Save the ordering prefix for the release menu.
switch($models["choicelist"]->_ordering)
{
	case "newcomments":
		$ordering_combobox_index = 0;
	break;

	case "mostvotes":
		$ordering_combobox_index = 1;		
		$navigation_subtabs_suffix = "most_popular/";
		$navigation_comboboxs_suffix .= "most_popular/";
	break;

	case "newuservotes":
		$ordering_combobox_index = 2;		
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
	$navbar_options["selected_entry"] = "none";
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


//echo outputPageTitle($title); 

?>

<div style="font-size:25px; padding-left:10px">
<?php echo $title; ?>
</div>


<br />


<table style="width:100%">
<tr><td style="padding-right:5px">
<?php
	//We prepare the tabbed menu.
	$menu = array();

	$menu["Summary"] = $basepath;
	$menu[(($data["user"]->uid == $GLOBALS['user']->uid)?t("My ideas"):t("Ideas"))] = $basepath . "ideas/" . $navigation_subtabs_suffix;
	$menu[(($data["user"]->uid == $GLOBALS['user']->uid)?t("My solutions"):t("Solutions"))] = $basepath . "solutions/" . $navigation_subtabs_suffix;
	$menu[(($data["user"]->uid == $GLOBALS['user']->uid)?t("Ideas I promoted"):t("Ideas promoted"))] = $basepath . "ideas_promoted/" . $navigation_subtabs_suffix;
	$menu[(($data["user"]->uid == $GLOBALS['user']->uid)?t("Ideas I demoted"):t("Ideas demoted"))] = $basepath . "ideas_demoted/" . $navigation_subtabs_suffix;
	$menu[(($data["user"]->uid == $GLOBALS['user']->uid)?t("Ideas I commented"):t("Ideas commented"))] = $basepath . "ideas_commented/" . $navigation_subtabs_suffix;
	$menu[(($data["user"]->uid == $GLOBALS['user']->uid)?t("My bookmarks"):t("Ideas bookmarked"))] = $basepath . "ideas_bookmarked/" . $navigation_subtabs_suffix;

	$tabmenu_data["entries"] = $menu;
	$tabmenu_data["selected_entry"] =  $navigation_subtabs_index;
	echo $this->loadTemplate("common/", "tabbedmenu", "", $tabmenu_data);
?>
</td>
<td style="text-align:right; width:1%">



<div class="rightbox">




<?php
	//Put and configure the ordering dropdown list
	$dropdown_data["entries"] = array
		(
			t("Latest comments") => $basepath_plus_subtabpath . $ordering_combobox_suffix,
			t("Most popular") => $basepath_plus_subtabpath . "most_popular/" . $ordering_combobox_suffix
		);
	//Disable for now, since the necessary SQL queries for it would be monstruous
	//if($tabmenu_show_my_latest_votes_ordering == true)
	//	$dropdown_data["entries"]["My latest votes"] = $basepath_plus_subtabpath . "my_latest_votes/" . $ordering_combobox_suffix;

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
