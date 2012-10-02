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

?>


<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 

<table class="ubuntu_roundnavbar_main" style="min-width:70px; line-height:15px; text-align: center;">
<tr><td style="padding:5px">


<span style="font-size:2.5em; font-weight:bold" id="approvalvotingnumber-<?php echo $data["item"]->id; ?>"><?php echo $data["item"]->totalapprovalvotes; ?></span><span style="font-size:16px"><b>/<?php echo QAPollConfig::getInstance()->getValue("choice_number_approvals_needed"); ?></b></span>
<br />
<span style="font-size:10px"><?php echo t('Approvals'); ?></span>
</td></tr><tr>
<td style="padding:0px">


<table><tr>
<?php if(UserModel::currentUserHasPermission("approve_idea", "Choice", $models["choicelist"], $data["item"]->id) || 
	UserModel::currentUserHasPermission("approve_idea", "Choice", $models["choice"])) : ?>
<td style="padding-right:0px; padding-left:5px; padding-bottom:5px">

<?php if($data["item"]->myapprovalvote == 0) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/edit/approve_idea/<?php echo $data["item"]->id; ?>/?destination=<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>" onclick="approvalVoteUp(<?php echo $data["item"]->id; ?>); return false;" id="linkapprovalvoteup-<?php echo $data["item"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/up22.png" alt="approve" title="<?php echo t('Approve the idea'); ?>" id="imageapprovalvoteup-<?php echo $data["item"]->id; ?>">
</a>
<?php else : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/up22-0.png" alt="approve" title="<?php echo t('You already approved the idea'); ?>" id="approve-<?php echo $data["item"]->id; ?>">
<?php endif; ?>

</td>
<?php endif; ?>

<?php if(UserModel::currentUserHasPermission("status_mark_as_nonidea", "Choice", $models["choicelist"], $data["item"]->id) ||
	UserModel::currentUserHasPermission("edit_status", "Choice", $models["choicelist"], $data["item"]->id) ||
	UserModel::currentUserHasPermission("status_mark_as_nonidea", "Choice", $models["choice"]) ||
	UserModel::currentUserHasPermission("edit_status", "Choice", $models["choice"])) : ?>
<td style="padding-left:2px; padding-right:2px; padding-bottom:5px">

<?php if($data["item"]->myapprovalvote == 0) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/edit/mark_as_invalid/<?php echo $data["item"]->id; ?>/?destination=<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>" onclick="markAsInvalidIdea(<?php echo $data["item"]->id; ?>); return false;" id="linknotanidea-<?php echo $data["item"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed.png" alt="down" title="<?php echo t('Mark as invalid idea'); ?>" id="imagenotanidea-<?php echo $data["item"]->id; ?>">
</a>
<?php else : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/closed-0.png" alt="down" title="<?php echo t('Mark as invalid idea'); ?>" id="imagenotanidea-<?php echo $data["item"]->id; ?>">
<?php endif; ?>

</td>
<?php endif; ?>

<?php if(UserModel::currentUserHasPermission("status_mark_as_already_implemented", "Choice", $models["choicelist"], $data["item"]->id) ||
	UserModel::currentUserHasPermission("edit_status", "Choice", $models["choicelist"], $data["item"]->id) ||
	UserModel::currentUserHasPermission("status_mark_as_already_implemented", "Choice", $models["choice"]) ||
	UserModel::currentUserHasPermission("edit_status", "Choice", $models["choice"])) : ?>
<td style="padding-left:0px; padding-right:5px; padding-bottom:5px">

<?php if($data["item"]->myapprovalvote == 0) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] ?>/edit/mark_as_already_implemented/<?php echo $data["item"]->id; ?>/?destination=<?php echo $GLOBALS['basemodule_path']; if($GLOBALS['basemodule_path'] != "") echo "/"; ?>" onclick="markAsAlreadyImplemented(<?php echo $data["item"]->id; ?>); return false;" id="linkalreadyimp-<?php echo $data["item"]->id; ?>">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/alreadyimplemented.png" alt="down" title="<?php echo t('Mark as already implemented'); ?>" id="imagealreadyimp-<?php echo $data["item"]->id; ?>">
</a>
<?php else : ?>
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/alreadyimplemented-0.png" alt="down" title="<?php echo t('Mark as already implemented'); ?>" id="imagealreadyimp-<?php echo $data["item"]->id; ?>">
<?php endif; ?>

</td>
<?php endif; ?>

</tr>
</table>

</td>
</tr>
</table>

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>

