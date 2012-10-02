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


<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = $selected_navbar_tab;
	$navbar_options["biglinks_prefix"] = $basepathnavbar;
	$navbar_options["title"] = t('Idea #!number activity', array("!number" => $data["choice"]->id));
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


<br />

<div style="padding:5px">

<?php
	//Put the log table here.
	echo $this->loadTemplate("common/", "logtable", "");
?>


</div>

