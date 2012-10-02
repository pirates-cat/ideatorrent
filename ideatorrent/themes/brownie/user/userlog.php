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
	$title = t("My activity log");
}
else
{
	$title = t("Contributor !name activity log", array("!name" => $data["user"]->name));
}


?>

<div style="font-size:25px; padding-left:10px">
<?php echo $title; ?>
</div>

<br />


<div style="padding:5px">

<?php
	//Put the log table here.
	echo $this->loadTemplate("common/", "logtable", "");
?>

</div>



</div>
<!-- QAPoll ends here -->

