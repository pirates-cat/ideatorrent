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

//
// Informations on the incoming data
// $data["show_report_links"]: -1 => Don't show the report links
// $data["show_admin_links"]: -1 => Don't show the admin links


?>	

<table width="100%">

<tr><td style="border: 1px solid #d8cfb7; background:#eaeada; border-bottom-width:0px; padding:5px">

<table>
<tr>
<td style="padding:0px; width:100%">



<?php if($data["comment"]->user_data->perm_level > 0) : ?>
<b>
<?php endif; ?>

<?php for($i = 0; $i < $data["comment"]->user_data->perm_level; $i++) : 
	?><img src="/<?php echo $this->getThemePath(); ?>/<?php echo $data["comment"]->user_data->perm_icon_name; ?>" /><?php
endfor; ?>

<a href="<?php echo $options["basepath"]; ?>contributor/<?php echo $data["comment"]->username; ?>/" class="authorlink"><?php echo $data["comment"]->username; ?>
</a>

<?php if($data["comment"]->user_data->perm_display_name != "") : ?>
<span style="font-size:x-small">(<?php echo t($data["comment"]->user_data->perm_display_name); 	?>)</span>
<?php endif; ?>

<?php if($data["comment"]->user_data->perm_level > 0) : ?>
</b>
<?php endif; ?>


<?php 
	echo t('wrote on the !date at !time',
		array("!date" => date('j M y',strtotime($data["comment"]->date)),
			"!time" =>date('H:i',strtotime($data["comment"]->date))
		));
?>


<?php if((user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole)) && $data["show_admin_links"] != -1) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path']; ?>/delete_comment/<?php echo $data["comment"]->id; ?>/" class="authorlink" style="font-size:x-small">[<?php echo t('delete'); ?>]</a>
<?php endif; ?>


</td>
<td style="width:1%; white-space:nowrap">
<?php if(user_access($GLOBALS['site']->getData()->userrole) && $data["show_report_links"] != -1) : ?>


<span style="font-size:x-small">
<?php echo t('Report as <a href="!link">spam</a> / <a href="!link2">offensive</a>',
		array("!link" => $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/report_spam_comment/" . $data["comment"]->id . "/",
			"!link2" => $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/report_offensive_comment/" . $data["comment"]->id . "/" 
			));
?>
</span>

<?php endif; ?>
</td>
</tr>
</table>



</td>
</tr>
<tr><td style="border: 1px solid #d8cfb7; padding:25px 15px 15px 15px; line-height:140%; border-top-width:0px">

<?php echo str_replace("\n", "<br />", force_text_wrap(linkify_URLS(strip_tags_and_evil_attributes($data["comment"]->comment, $this->getThemeSetting("item_comment_auth_tags"))))); ?>

</td></tr>
</table>

<br />
