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



?>{"items": 
	[

<?php for ($i = 0; $i < count($this->_data->items); $i++) : ?>

<?php
	$this->item =& $this->_data->items[$i];
	echo $this->loadNonThemedTemplate("choicelist/", "json", "item");
	if($i != count($this->_data->items) - 1)
		echo ",";
?>

<?php endfor; ?>

	],

"pagenumber": "<?php 

	//Page number
	echo $this->_data->page;

?>",

"totalpages": "<?php 

	//Total number of pages
	echo ceil($this->_data->rowCount / $this->_data->numberRowsPerPage);

?>"
}


