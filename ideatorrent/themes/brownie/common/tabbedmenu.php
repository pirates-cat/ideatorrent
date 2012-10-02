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

<?php if(count($data["entries"]) > 0) : ?>


<ul class="TabbedMenu">
<?php 
	$i = 0;
	foreach($data["entries"] as $entry => $link) : 
?>

	<?php if($i == $data["selected_entry"]) : ?>
				<li id="TabbedMenuActive"><?php echo $entry; ?></li>
	<?php else : ?>
				<li><a href="<?php echo $link; ?>"><?php echo $entry; ?></a></li>
	<?php
		endif; 
		$i++;
	?>

<?php endforeach; ?>
</ul>

<?php endif; ?>
