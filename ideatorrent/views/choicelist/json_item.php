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
// Generate one JSON line per item.
// Votes | title | description | date | status
//


?>

{"votes": "<?php
	//Nb of votes
	echo $this->item->votes; 


?>", "id": "<?php 

	//Date
	echo $this->item->id;

?>", "title": "<?php 

	//Title
	?><a target=\"_blank\" id=\"title-<?php echo $this->item->id; ?>\" class=\"<?php echo ($this->item->status == -2)?"itemdeleted":"itemundeleted"?>\" href=\"<?php echo $GLOBALS['basemodule_url'] ?>/idea/<?php echo $this->item->id; ?>/\"><?php echo str_replace('"', '\\"', force_text_wrap(strip_tags_and_evil_attributes($this->item->title), 30)); ?></a>"


, "description": "<?php

	//Description
	if($this->item->description != null)
	{
		$occurs = array("\r\n", "\n", "\r", '"');
		$replaces = array("<br />", "<br />", "<br />", '\\"');
		echo str_replace($occurs, $replaces, limit_number_of_lines(force_text_wrap(strip_tags_and_evil_attributes($this->item->description,
			$this->getThemeSetting("item_description_auth_tags"))), 30, $GLOBALS['basemodule_url'] . "/" . $pathname .
			"/" . $this->item->id));
	}
	else
		echo "[No description]";
?>", "date": "<?php 

	//Date
	echo date('j-M-y H:i',strtotime($this->item->date))

?>", "status": "<?php 
	
	//Status
	echo str_replace('"', '\\"', getStatusString($this->item->status, $this->item->bugstatus, $this->item->specstatus, $this->item->specdelivery, $this->item->duplicatenumber));

?>", "search_rank": "<?php 

	//Date
	echo round($this->item->search_rank * 100) . "%";

?>"}
