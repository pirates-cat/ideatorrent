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

?>	


<b class="ubuntu_title">
<b class="ubuntu_title1"><b></b></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title5"></b></b>


<table width="100%" class="ubuntu_title_main"><tr><td>
<h1 style="padding:10px 0px 0px 10px; margin: 0px 0px 0px 0px">Advanced search</h1><br />

</td></tr></table>


<b class="ubuntu_title">
<b class="ubuntu_title5"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title1"><b></b></b></b>


<br />
<br />

<form method="get" action="">

<table><tr><td colspan="2">


<fieldset class="entryfldset">
	<legend><b>Keywords</b></legend>
	<div>
	<input style="width:400px;" type="text" name="keywords" value="<?php echo $_GET['keywords'] ?>"/>
	</div>
</fieldset>


</td></tr><tr><td>


<fieldset class="entryfldset">
	<legend><b>Ordering</b></legend>
	<div>
	<select name="ordering" >
	<option value="mostvotes" <?php if($_GET['ordering'] == "mostvotes") echo "selected=\"selected\" " ?>>Most votes first</option>
	<option value="leastvotes" <?php if($_GET['ordering'] == "leastvotes") echo "selected=\"selected\" " ?>>Least votes first</option>
	<option value="mosthype-day" <?php if($_GET['ordering'] == "mosthype-day") echo "selected=\"selected\" " ?>>Most popular today</option>
	<option value="mosthype-week" <?php if($_GET['ordering'] == "mosthype-week") echo "selected=\"selected\" " ?>>Most popular this week</option>
	<option value="mosthype-month" <?php if($_GET['ordering'] == "mosthype-month") echo "selected=\"selected\" " ?>>Most popular this month</option>
	<option value="new" <?php if($_GET['ordering'] == "new") echo "selected=\"selected\" " ?>>Newer first</option>
	<option value="old" <?php if($_GET['ordering'] == "old") echo "selected=\"selected\" " ?>>Older first</option>
	<option value="random" <?php if($_GET['ordering'] == "random") echo "selected=\"selected\" " ?>>Randomly</option>
	</select>
	</div>
</fieldset>


</td><td>


<?php if (count($this->_relationList) != 0 && $GLOBALS['entrypoint']->getData()->filterArray['relation'] == null) : ?>
<fieldset class="entryfldset">
	<legend><b>Related to:</b></legend>
	<table>
	<tr><td style="padding:0 0 0 0">
	<select name="relation" id="relations">
	<option value="-2" <?php if($_GET['choice_relation'] == -2) echo "selected=\"selected\" " ?>>Anything</option>
	<option value="-1" <?php if($_GET['choice_relation'] == -1) echo "selected=\"selected\" " ?>>Nothing/Others</option>

<?php for ($i = 0; $i < count($this->_relationList); $i++) : ?>
<?php
	if($i == 0 || $current_relation_cat != $this->_relationList[$i]->relation_cat_name)
		echo "<optgroup label=\"" . $this->_relationList[$i]->relation_cat_name . "\">";
	$current_relation_cat = $this->_relationList[$i]->relation_cat_name;
?>

	<option value="<?php echo $this->_relationList[$i]->relation_id; ?>" <?php if(($this->_data != null && $_POST['_choice_submitted'] == null && $this->_data->relation_id == $this->_relationList[$i]->relation_id) || $_POST['choice_relation'] == $this->_relationList[$i]->relation_id) echo "selected=\"selected\" " ?> label="<?php echo $this->_relationList[$i]->relation_name; ?>"><?php echo $this->_relationList[$i]->relation_name; ?></option>

<?php
	if($i == count($this->_relationList) - 1 || $current_relation_cat != $this->_relationList[$i+1]->relation_cat_name)
		echo "</optgroup>";
?>
<?php endfor; ?>

	</select>
	</td><td id="choice_category_tooltip" style="padding-left:10px">	

	</td></tr>
	</table>
</fieldset>
<?php endif; ?>

<?php if($GLOBALS['entrypoint']->getData()->filterArray['relation'] != null) : ?>
<input type="hidden" name="relation" value="<?php echo $GLOBALS['entrypoint']->getData()->filterArray['relation']; ?>" />
<?php endif; ?>


</td></tr><tr><td style="vertical-align:top">


<fieldset class="entryfldset">
	<legend><b>Status</b></legend>
	<div>
	<input type="checkbox" name="state_new" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['state_new'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> New
	<br />
	<input type="checkbox" name="state_needinfos" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['state_needinfos'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> Needs clarification
	<br />
	<input type="checkbox" name="state_blueprint_approved" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['state_blueprint_approved'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> Blueprint approved
	<br />
	<input type="checkbox" name="state_workinprogress" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['state_workinprogress'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> In development
	<br />
	<input type="checkbox" name="state_done" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['state_done'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> Implemented
	<br />
	<input type="checkbox" name="state_already_done" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['state_already_done'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> Already implemented
	<br />
	<input type="checkbox" name="state_unapplicable" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['state_unapplicable'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> Won't implement
<?php if(user_access($site->getData()->adminrole)) : ?>
	<br />
	<input type="checkbox" name="state_deleted" value="1" <?php if($_GET['state_deleted'] != null) echo "checked=\"checked\""; ?> /> Deleted
<?php endif; ?>
	</div>
</fieldset>


</td><td style="vertical-align:top">


<fieldset class="entryfldset">
	<legend><b>Attachments</b></legend>
	<div>
	<input type="checkbox" name="nothing_attached" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['nothing_attached'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> No attachment
	<br />
	<div style="margin-bottom:10px" ></div>
	<input type="checkbox" name="bug_attached" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['bug_attached'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> A bug <img src="/modules/qapoll/images/bug.png" alt="bug" />
	<br />
	<input type="checkbox" name="spec_attached" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['spec_attached'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> A blueprint <img src="/modules/qapoll/images/spec.png" alt="forum">
	<br />
	<input type="checkbox" name="thread_attached" value="1" <?php if(($_GET['_search_submitted'] != null && $_GET['thread_attached'] != null) || $_GET['_search_submitted'] == null) echo "checked=\"checked\""; ?> /> A forum thread <img src="/modules/qapoll/images/forum.png" alt="forum">
	<br />
	<div style="margin-bottom:5px" ></div>
	<select name="attachment_operator" >
	<option value="0" <?php if($_GET['attachment_operator'] == "0") echo "selected=\"selected\" " ?>>Any of the attachments</option>
	<option value="1" <?php if($_GET['attachment_operator'] == "1") echo "selected=\"selected\" " ?>>All the attachments</option>
	</select>
	</div>
</fieldset>


</td></tr>

<tr><td colspan="2">

<fieldset class="entryfldset">
	<legend><b>Tags</b></legend>
	<div>
	<select name="tags_operator">
		<option value="and" <?php if($_GET['tags_operator'] == "and" || $_GET['tags_operator'] == null) echo "selected=\"selected\" " ?>>All of</option>
		<option value="or" <?php if($_GET['tags_operator'] == "or") echo "selected=\"selected\" " ?>>Any of</option>
	</select>
	<input  style="width:300px;" type="text" name="tags" value="<?php echo $_GET['tags'] ?>">
	</div>
</fieldset>

</td></tr>


<?php if(user_access($site->getData()->adminrole) || user_access($site->getData()->developerrole)) : ?>
<tr><td colspan="2">

<fieldset class="entryfldset">
	<legend><b>Admin tags</b></legend>
	<div>
	<select name="admintags_operator">
		<option value="and" <?php if($_GET['admintags_operator'] == "and" || $_GET['admintags_operator'] == null) echo "selected=\"selected\" " ?>>All of</option>
		<option value="or" <?php if($_GET['admintags_operator'] == "or") echo "selected=\"selected\" " ?>>Any of</option>
	</select>
	<input  style="width:300px;" type="text" name="admintags" value="<?php echo $_GET['admintags'] ?>">
	</div>
</fieldset>

</td></tr>
<?php endif; ?>

<tr><td>


<?php if (count($this->_categoryList) != 0 && $GLOBALS['entrypoint']->getData()->filterArray['relation'] == null) : ?>
<fieldset class="entryfldset">
	<legend><b>Category</b></legend>
	<table>
	<tr><td style="padding:0 0 0 0">
	<select name="category" onchange="show_cat_tooltip()" id="categories">
	<option value="-1" <?php if($_GET['choice_category'] == -1) echo "selected=\"selected\" " ?>>Any category</option>

<?php for ($i = 0; $i < count($this->_categoryList); $i++) : ?>

	<option value="<?php echo $this->_categoryList[$i]->id; ?>" <?php if($_GET['category'] == $this->_categoryList[$i]->id) echo "selected=\"selected\" " ?>><?php echo $this->_categoryList[$i]->name; ?></option>

<?php endfor; ?>

	</select>
	</td><td id="choice_category_tooltip" style="padding-left:10px">	

	</td></tr>
	</table>
</fieldset>
<?php elseif($GLOBALS['entrypoint']->getData()->filterArray['relation'] != null && count($this->_relation_subcategories) != 0) : ?>
<fieldset class="entryfldset">
	<legend><b>Category</b></legend>
	<table>
	<tr><td style="padding:0 0 0 0">
	<select name="relation_subcategory_id" id="relation_subcategory_id">
	<option value="-1" <?php if($_GET['choice_category'] == -1) echo "selected=\"selected\" " ?>>Any category</option>

<?php for ($i = 0; $i < count($this->_relation_subcategories); $i++) : ?>

	<option value="<?php echo $this->_relation_subcategories[$i]->id; ?>" <?php if($_GET['category'] == $this->_relation_subcategories[$i]->id) echo "selected=\"selected\" " ?>><?php echo $this->_relation_subcategories[$i]->name; ?></option>

<?php endfor; ?>

	</select>
	</td><td id="choice_category_tooltip" style="padding-left:10px">	

	</td></tr>
	</table>
</fieldset>
<?php endif; ?>



</td><td>

<fieldset class="entryfldset">
	<legend><b>Others</b></legend>
	<div>
	<input type="checkbox" name="duplicate_items" value="-2" <?php if(($_GET['_search_submitted'] != null && $_GET['type_bug'] != null)) echo "checked=\"checked\""; ?> /> Show duplicates
	</div>
</fieldset>




</td></tr></table>


<?php if($GLOBALS['entrypoint']->getData()->filterArray['type_bug'] != null && $GLOBALS['entrypoint']->getData()->filterArray['type_bug'] == 0) : ?>

<input type="hidden" name="type_idea" value="1" />

<?php elseif($GLOBALS['entrypoint']->getData()->filterArray['type_idea'] != null && $GLOBALS['entrypoint']->getData()->filterArray['type_idea'] == 0) : ?>

<input type="hidden" name="type_bug" value="1" />

<?php else : ?>

<?php endif; ?>

<br />

<input type="hidden" name="_search_submitted" value="true" />
<input type="submit" value="Search" id="submitbutton" />
</form>


