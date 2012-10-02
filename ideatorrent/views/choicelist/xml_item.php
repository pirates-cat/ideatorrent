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
<idea id="<?php echo $this->item->id; ?>">
      <title><?php echo xmlencode($this->item->title); ?></title>
      <link><?php echo xmlencode($GLOBALS['basemodule_url'] . "/idea/" . $this->item->id . "/"); ?></link>
	<author id="<?php echo $this->item->userid; ?>"><?php echo xmlencode($this->item->username); ?></author>
	<global-category id="<?php echo $this->item->categoryid; ?>"><?php echo xmlencode($this->item->catname); ?></global-category>
	<relation id="<?php echo $this->item->relation_id; ?>"><?php echo xmlencode($this->item->relation_name); ?></relation>
	<relation-category id="<?php echo $this->item->relation_subcategory_id; ?>"><?php echo xmlencode($this->item->relationsubcatname); ?></relation-category>
	<status id="<?php echo $this->item->status; ?>"><?php echo xmlencode(getStatusString($this->item->status)); ?></status>
	<duplicate-of><?php echo $this->item->duplicatenumber; ?></duplicate-of>
	<target-release id="<?php echo $this->item->release_target; ?>"><?php echo xmlencode($this->item->releasename); ?></target-release>
      <date><?php echo date("r", strtotime($this->item->date)); ?></date>
      <description><?php echo xmlencode($this->item->description); ?></description>
	<developer-comment><?php echo xmlencode($this->item->whiteboard); ?></developer-comment>
	<solution-max-votes><?php echo $this->item->votes; ?></solution-max-votes>

	<solutions>
		<?php for ($i = 0; $i < count($this->item->solutions); $i++) : ?>

		<?php
		$this->solution =& $this->item->solutions[$i];
		echo $this->loadNonThemedTemplate("choicelist/", "xml", "solution");
		?>

		<?php endfor; ?>
	</solutions>

</idea>
