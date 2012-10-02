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
		<solution id="<?php echo $this->solution->id; ?>" idea-solution-id="<?php echo $this->solution->solution_number; ?>">
			<title><?php echo xmlencode($this->solution->title); ?></title>
			<author id="<?php echo $this->solution->userid; ?>"><?php echo xmlencode($this->item->username); ?></author>
			<status id="<?php echo $this->solution->status; ?>"><?php if($this->solution->status == 1) echo "New"; else echo "Deleted"; ?></status>
			<duplicate-of><?php echo $this->solution->duplicate_choice_solution_id; ?></duplicate-of>
			<date><?php echo date("r", strtotime($this->solution->date)); ?></date>
			<description><?php echo xmlencode($this->solution->description); ?></description>
			<votes><?php echo $this->solution->solution_votes; ?></votes>
			<votes-plus-duplicates-votes><?php echo $this->solution->total_votes; ?></votes-plus-duplicates-votes>

		</solution>
