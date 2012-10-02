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
<?php echo t('Process the duplicate reports'); ?>
</h1><br />

</td></tr></table>


<b class="ubuntu_title">
<b class="ubuntu_title5"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title1"><b></b></b></b>






<br />


<?php if($data["duplicate_report_list"]->page == 1) : ?>

<b><?php echo t('PLEASE BE CAREFUL:'); ?></b> 
<ul>
<li><?php echo t('Mark an idea as duplicate only if the rationale is identical or *almost* identical.'); ?></li>
<li><?php echo t("When marking as duplicate, the solutions of the duplicate idea won't be linked to the master idea. If you find an interesting solution in the duplicate idea, link it first to the master idea."); ?></li>
<li><?php echo t("Before marking as duplicate, if you find a popular solution in the duplicate idea, but which is a duplicate of a solution in the master idea, please mark the solution as duplicate of the corresponding solution. That way, the votes will be merged."); ?></li>
<li><?php echo t("While processing duplicates with AJAX, the data coherence is not assured. As such, if some actions do not answer, it is likely they are no more valid. You can reload the page to fix that."); ?></li>
</ul>

<br />

<?php endif; ?>

<table style="width: 100%">

<?php if(count($this->_data->items) == 0) : ?>

<tr><td style="text-align:center">
<span style="font-weight:bold"><?php echo t("No entries."); ?></span>
</td></tr>

<?php else : ?>

<?php for ($i = 0; $i < count($this->_data->items); $i++) : ?>

<?php
$item_data = array();
$item_data["item"] =& $data["duplicate_report_list"]->items[$i];
$this->item =& $data["duplicate_report_list"]->items[$i];
echo $this->loadTemplate("duplicate_reportlist/", "default", "item", $item_data);
?>

<?php endfor; ?>



<?php endif; ?>

</table>

<div style="text-align:center">
<?php 

	//Prepare the data and call the pagination template.
	$pagination_data = array();
	$pagination_data["rowCount"] = $data["duplicate_report_list"]->rowCount;
	$pagination_data["page"] = $data["duplicate_report_list"]->page;
	$pagination_data["numberRowsPerPage"] = $data["duplicate_report_list"]->numberRowsPerPage;
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
<!-- QAPoll right block ends here -->

