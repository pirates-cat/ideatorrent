<?php 
/*
Copyright (C) 2007 Nicolas Deschildre <ndeschildre@gmail.com>

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

<div id="choice-solution-<?php echo $data["item"]->id; ?>-<?php echo $data["leftid"]; ?>-<?php echo $data["item_solution"]->id; ?>">
<b>
<?php 
	echo t('Solution #!number: !title (!nbvotes votes)',
		array("!number" => $data["item_solution"]->solution_number,
			"!title" => strip_tags_and_evil_attributes($data["item_solution"]->title),
			"!nbvotes" => $data["item_solution"]->total_votes
			));
?>
</b>

<br />


<?php if($data["show_admin_links"] == 1) : ?>
<div id="adminlinks-<?php echo $data["item"]->id; ?>-<?php echo $data["item_solution"]->id; ?>">

<div id="linksolutionlink-<?php echo $data["item"]->id; ?>-<?php echo $data["item_solution"]->id; ?>">
<a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/link_solution_to/" . $data["item_solution"]->id . "/" . $data["rightid"]; ?>" onclick="link_solution_to(<?php echo $data["item"]->id; ?>, <?php echo $data["leftid"]; ?>, <?php echo $data["item_solution"]->id; ?>, <?php echo $data["rightid"]; ?>); return false;">[<?php echo t('Link to the other idea'); ?>]</a>
</div>

<?php foreach($data["other_item_solutions"] as $other_sol) : ?>
<a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $GLOBALS['basemodule_path'] . "/mark_solution_as_dup/" . $data["item_solution"]->id . "/" . $other_sol->id; ?>" onclick="mark_solution_as_dup(<?php echo $data["item"]->id; ?>, <?php echo $data["leftid"]; ?>, <?php echo $data["item_solution"]->id; ?>, <?php echo $other_sol->id; ?>); return false;">[<?php echo t("Mark as dup of the other idea's solution #!number", array("!number" => $other_sol->solution_number)); ?>]</a><br />
<?php endforeach; ?>

</div>
<?php endif; ?>



<div id="solution-description-<?php echo $data["item_solution"]->id; ?>">
<?php 

//Text contains the text separated in two if it is > 30 lines.
$text = split_text_number_of_lines(force_text_wrap(linkify_URLS(strip_tags_and_evil_attributes($data["item_solution"]->description, $this->getThemeSetting("item_description_auth_tags")))), $this->getThemeSetting("choice_solution_max_visible_lines"));
$text[0] = str_replace("\n", "<br />", $text[0]);
$text[1] = str_replace("\n", "<br />", $text[1]);

echo $text[0];

?>
<?php if($text[1] != "") : ?>

<div id="hidden-choice-solution-link-<?php echo $data["item_solution"]->id; ?>" class="hidden-choice-solution-link">
<a href="#" onclick="showSecondPart(<?php echo $data["item_solution"]->id; ?>); return false;">[...]</a>
</div>

<div id="hidden-choice-solution-<?php echo $data["item_solution"]->id; ?>" class="hidden-choice-solution">
<?php echo $text[1]; ?>
</div>

<?php endif; ?>
</div>
<br />

</div>
