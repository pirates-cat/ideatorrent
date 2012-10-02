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

<!-- QAPoll starts here -->
<div class="qapoll">

<?php
	//Put the top navigation bar
	$navbar_data = array();
	$navbar_models = array();
	$navbar_options = array();
	$navbar_options["selected_entry"] = "ingestation";
	$navbar_options["biglinks_prefix"] = $basepath;
	$navbar_options['title'] = t('Submit your idea (Step 2 of 3)');
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





<div style="padding-left:5px">




<br />

<form method="post" action="">
<div style="font-size:1.2em">
<?php echo t('Please describe your idea in a few words, for example, "audio preview in Nautilus":'); ?>
</div>
<br />
<fieldset class="entryfldset">
	<legend><b><?php echo t('Title'); ?></b></legend>
	<div><input style="width:400px;" type="text" name="keywords" maxlength="80" id="dup_search_string" value="<?php echo htmlentities($_POST['keywords']); ?>" />

<input type="hidden" name="_keywords_submitted" value="true" />
<input type="submit" name="choice_submit" value="<?php echo t('Check for duplicates'); ?>" onclick="update_dup_table('', 1, 'dup_search_string', 'submitpage2_showNextButton'); return false;" />
</div>
</fieldset>
</form>

<fieldset class="entryfldset" id="duptable">
	<legend><b><?php echo t('Possible duplicates'); ?></b></legend>
	<div style="padding-top:5px"></div>
<?php
	//Put the top navigation bar
	$duptable_data["duplist"] = $data['duplist'];
	echo $this->loadTemplate("common/", "duplicatetable", "", $duptable_data);

?>
</fieldset>



<div id="submitpage2-nextstepbuttonarea">

<form method="get" action="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/3"; ?>">
<div style="font-size:1.2em">
<?php echo t('Please look at these ideas. If you have not found an idea similar to the one you want to submit, please continue.'); ?>
</div>
<br />

<input type="submit" value="<?php echo t('Continue'); ?>" id="submitpage2-nextstepbutton" <?php if($data['duplist'] == null) : ?>disabled="disabled"<?php endif; ?>  />
</form>
<br />

</div>




</div>

</div>
<!-- QAPoll ends here -->
