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

<?php if($data["choice"]->duplicatenumber != -1 || $data["choice"]->status == 1 || $data["choice"]->status == 2 || $data["choice"]->status == 3 || $data["choice"]->status == 4 || $data["choice"]->status == 5 || $data["choice"]->status == 6 || $data["choice"]->status == 7 || $data["choice"]->status == 8) :?>


<b class="notice_div">
<b class="notice_div1"><b></b></b>
<b class="notice_div2"><b></b></b>
<b class="notice_div3"></b>
<b class="notice_div4"></b>
<b class="notice_div5"></b></b> 
<div class="notice_div_main" style="padding:5px 5px 5px 10px">

<table width="100%"><tr><td style="width:1%; padding-right:5px">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/info.png" alt="bug"/>
</td><td>
<span style="font-weight:bold">
<?php if($data["choice"]->duplicatenumber != -1) : ?>
	<?php 
		echo t('This idea is a duplicate of <a href="!idealink">Idea #!number: !ideatitle</a>.',
			array("!idealink" => $GLOBALS['basemodule_url'] . "/idea/" . $data["choice"]->duplicatenumber . "/",
				"!number" => $data["choice"]->duplicatenumber,
				"!ideatitle" => strip_tags_and_evil_attributes($data["choice"]->duptitle)));
	?>

	<?php if(user_access($site->getData()->adminrole) || user_access($site->getData()->moderatorrole) ||
		user_access($site->getData()->developerrole)): ?>
		<a href="<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $data["choice"]->id; ?>/unduplicate/"><?php echo t('Unduplicate this idea'); ?></a>.
	<?php endif; ?>

<?php else: ?>
	<?php if($data["choice"]->status == 1) :?>
		<?php if($data["choice"]->last_status_change != null) : ?>

			<?php echo t('This idea was marked as needing a more complete blueprint the !date.', 
				array("!date" => date('j F y',strtotime($data["choice"]->last_status_change)))); ?>

		<?php else : ?>

			<?php echo t('This idea is marked as needing a more complete blueprint.'); ?>

		<?php endif; ?>

	<?php endif; ?>
	<?php if($data["choice"]->status == 2) :?>
		<?php if($data["choice"]->last_status_change != null) : ?>

			<?php echo t('This idea was marked as being in development the !date.', 
				array("!date" => date('j F y',strtotime($data["choice"]->last_status_change)))); ?>

		<?php else : ?>

			<?php echo t('This idea is marked as being in development.'); ?>

		<?php endif; ?>
		<?php if($data["choice"]->target_release_name != null) : ?>

			<?php echo t('Target release: !target.', array("!target" => $data["choice"]->target_release_name)); ?>

		<?php endif; ?>
	<?php endif; ?>
	<?php if($data["choice"]->status == 3) : ?>
		<?php if($data["choice"]->last_status_change != null) : ?>

			<?php echo t('This idea was marked as implemented the !date.', 
				array("!date" => date('j F y',strtotime($data["choice"]->last_status_change)))); ?>

		<?php else : ?>

			<?php echo t('This idea is marked as implemented.'); ?>

		<?php endif; ?>
		<?php if($data["choice"]->target_release_name != null) : ?>

			<?php echo t('Available starting !target.', array("!target" => $data["choice"]->target_release_name)); ?>

		<?php endif; ?>
	<?php endif; ?>
	<?php if($data["choice"]->status == 4) : ?>
		<?php if($data["choice"]->last_status_change != null) : ?>

			<?php echo t('This idea was marked as being not considered for implementation the !date.', 
				array("!date" => date('j F y',strtotime($data["choice"]->last_status_change)))); ?>

		<?php else : ?>
			<?php echo t('This idea is marked as being not considered for implementation.'); ?>
		<?php endif; ?>
	<?php endif; ?>
	<?php if($data["choice"]->status == 5) : ?>
		<?php if($data["choice"]->last_status_change != null) : ?>

			<?php echo t('This idea was marked as already implemented the !date.', 
				array("!date" => date('j F y',strtotime($data["choice"]->last_status_change)))); ?>

		<?php else : ?>

			<?php echo t('This idea is marked as already implemented.'); ?>

		<?php endif; ?>
		<?php if($data["choice"]->target_release_name != null) : ?>

			<?php echo t('Available starting !target.', array("!target" => $data["choice"]->target_release_name)); ?>

		<?php endif; ?>
	<?php endif; ?>
	<?php if($data["choice"]->status == 6) : ?>
		<?php if($data["choice"]->last_status_change != null) : ?>

			<?php echo t('This idea was marked as having a complete blueprint the !date.', 
				array("!date" => date('j F y',strtotime($data["choice"]->last_status_change)))); ?>

		<?php else : ?>

			<?php echo t('This idea is marked as having a complete blueprint.'); ?>

		<?php endif; ?>
	<?php endif; ?>
	<?php if($data["choice"]->status == 7) : ?>
		<?php if($data["choice"]->last_status_change != null) : ?>

			<?php echo t('This entry was marked as not being an idea the !date.', 
				array("!date" => date('j F y',strtotime($data["choice"]->last_status_change)))); ?>

		<?php else : ?>

			<?php echo t('This entry is marked as not being an idea.'); ?>

		<?php endif; ?>
	<?php endif; ?>
	<?php if($data["choice"]->status == 8) : ?>

		<?php echo t('This idea is awaiting moderator approval before going to the <a href="!link">popular ideas area</a>.',
			array("!link" => $GLOBALS['basemodule_url'] . $GLOBALS['basemodule_prefilter_path'])); ?>

	<?php endif; ?>
<?php endif; ?>
</span>
</td><td>
</td></tr></table>


</div>
<b class="notice_div">
<b class="notice_div5"></b>
<b class="notice_div4"></b>
<b class="notice_div3"></b>
<b class="notice_div2"><b></b></b>
<b class="notice_div1"><b></b></b></b>
<div style="margin-bottom:15px"></div>

<?php endif; ?>

