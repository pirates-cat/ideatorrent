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

<b>[<?php echo $this->solution->total_votes; ?> votes] Solution #<?php echo $this->solution->solution_number; ?>: <?php echo strip_tags_and_evil_attributes($this->solution->title); ?></b>
<br />

<?php
if($this->item->description != null)
	echo str_replace("\n", "<br />", strip_tags_and_evil_attributes($this->solution->description, $this->getThemeSetting("item_description_auth_tags")));
?>
<br />
<br />
