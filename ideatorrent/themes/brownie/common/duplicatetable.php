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

//The AJAX update is triggered via update_dup_table();
//If a $data["duplist"] is given, fill the table with its contents
// $data["table_id"]: An id to give to all the table elements, if several tables are present on a same page


?>


<table style="width:600px; text-align:center" id="duptable<?php echo $data["table_id"]; ?>-all">
	<tr style="border-bottom: 1px solid black" class="duptableheader"><td><?php echo t('Number'); ?></td><td><?php echo t('Title'); ?></td><td><?php echo t('Votes'); ?></td><td><?php echo t('Date'); ?></td><td><?php echo t('Status'); ?></td><td><?php echo t('Relevance'); ?></td></tr>


<?php for($i = 1; $i < 11; $i++) : ?>
<tr>


	<td style="background-color:#f7f7f7; padding:5px; height:45px" id="duptable<?php echo $data["table_id"]; ?>-cell-<?php echo $i; ?>-1">
	<?php if($data["duplist"] != null && $data["duplist"]->items[$i-1] != null) echo $data["duplist"]->items[$i-1]->id; ?>
	&nbsp;</td>

	<td style="padding:5px" id="duptable<?php echo $data["table_id"]; ?>-cell-<?php echo $i; ?>-2">
	<?php if($data["duplist"] != null && $data["duplist"]->items[$i-1] != null) : ?>
	<a href="<?php echo $GLOBALS['basemodule_url'] . "/idea/" . $data["duplist"]->items[$i-1]->id . "/"; ?>" target="_blank">
	<?php echo force_text_wrap(strip_tags_and_evil_attributes($data["duplist"]->items[$i-1]->title), 30); ?></a>
	<?php endif; ?>
	</td>

	<td style="background-color:#f7f7f7; padding:5px; height:45px" id="duptable<?php echo $data["table_id"]; ?>-cell-<?php echo $i; ?>-3">
	<?php if($data["duplist"] != null && $data["duplist"]->items[$i-1] != null) echo $data["duplist"]->items[$i-1]->votes; ?>
	</td>

	<td style="padding:5px; white-space:nowrap" id="duptable<?php echo $data["table_id"]; ?>-cell-<?php echo $i; ?>-4">
	<?php if($data["duplist"] != null && $data["duplist"]->items[$i-1] != null) echo date('j-M-y H:i',strtotime($data["duplist"]->items[$i-1]->date)); ?>
	</td>

	<td style="background-color:#f7f7f7;padding:5px" id="duptable<?php echo $data["table_id"]; ?>-cell-<?php echo $i; ?>-5">
	<?php if($data["duplist"] != null && $data["duplist"]->items[$i-1] != null) echo getStatusString($data["duplist"]->items[$i-1]->status); ?>
	</td>

	<td style="padding:5px" id="duptable<?php echo $data["table_id"]; ?>-cell-<?php echo $i; ?>-6">
	<?php if($data["duplist"] != null && $data["duplist"]->items[$i-1] != null) echo round($data["duplist"]->items[$i-1]->search_rank * 100) . "%"; ?>
	&nbsp;</td>
	</tr>

<?php endfor; ?>

<tr>
<td colspan="6" style="text-align:center; padding-top:5px; color:grey; border-top: 1px solid black">
<span style="display:none" id="duptable<?php echo $data["table_id"]; ?>-leftrightlinks">
<a href="#" id="duptable<?php echo $data["table_id"]; ?>-leftlink" onclick="dup_table_prev_page(<?php echo $data["table_id"]; ?>); return false;">[&lt;&lt; prev]</a> 
<a href="#" id="duptable<?php echo $data["table_id"]; ?>-rightlink" onclick="dup_table_next_page(<?php echo $data["table_id"]; ?>); return false;">[next &gt;&gt;]</a></span>
&nbsp;<span style="display:none" id="duptable<?php echo $data["table_id"]; ?>-pagenumber"></span>
<span style="display:none" id="duptable<?php echo $data["table_id"]; ?>-searchstring"></span>
</td>
</tr>
</table>


