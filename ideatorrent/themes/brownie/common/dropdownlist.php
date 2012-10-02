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

<table style="width:219px">
<tr><td style="vertical-align:top; padding-right:0px">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/dropdownlist-left.png" />
</td><td style="vertical-align:top; padding:0px">
<ul id="dropdown-1">
	<li><a href="#"><?php $keys = array_keys($data["entries"]); echo $keys[$data["selected_entry"]]; ?></a>
		<ul class="dropdown-2">
			<?php foreach($data["entries"] as $entry_name => $link) : ?>
			<li><a href="<?php echo $link; ?>"><?php echo $entry_name; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</li>
</ul>
</td><td style="vertical-align:top; padding-left:0px">
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/dropdownlist-right.png" />
</td></tr></table>
