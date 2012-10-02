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
// Required for this template:
// $models["selected_relation"] : The model of the selected relation, if any.
// $models["selected_relationsubcat"] : The model of the selected relation subcategory, if any.
// $models['selected_category'] : The model of the selected global category, if any.
// $options["biglinks_prefix"] : The prefix of the links in the big tabs.
// $options["biglinks_suffix"] : The suffix of the links in the big tabs.
// $options["comboboxs_suffix"] : The suffix of the links in the dropdown menus.
// $options["selected_entry"] : The selected big tab.
// $options["title"] : The title to use
// $data["relationlist"] : All the available relations.
// $data["relationsubcategorylist"] : All the subcategories of the selected relation (if any)
// $data["categorylist"] : All the available global categories.
//


//Compute here which elements of the topbar gets selected
$all_selected = "";
$relation_selected = "";
$category_selected = "";

if($models["selected_relation"] == null && $models["selected_relationsubcat"] == null && $models['selected_category'] == null)
	$all_selected = "selected";
if($models["selected_relation"] != null)
	$relation_selected = "selected";
if($models["selected_relationsubcat"] != null || $models['selected_category'] != null)
	$category_selected = "selected";


?>

<b class="ubuntu_title">
<b class="ubuntu_title1"><b></b></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title5"></b></b>


<table style="width:100%">


<tr><td colspan="6" class="ubuntu_title_main" style="padding-left:5px; padding-top:0px; padding-bottom:0px">
	<ul id="maindropdown-1">
		<li class="maindropdown-1nodrop <?php echo $all_selected; ?>"><a href="<?php echo $GLOBALS['basemodule_url'] . "/"; ?><?php echo $options["comboboxs_suffix"]; ?>"><?php echo t('All'); ?> <i>more</i></a></li>

<?php
	//Show only the relation dropdown list if we have relations in the database. Otherwise it's no use!
	if(count($data["relationlist"]) > 0) : 
?>

		<li class="maindropdown-1drop <?php echo $relation_selected; ?>" id="maindropdown-menu1" ><a id="maindropdown-1link" onclick="activate_menu1();return false;" href="<?php
		if($models["selected_relation"] != null)
			echo $GLOBALS['basemodule_url'] . "/" . $models["selected_relation"]->getData()->url_name . "/" . $options["comboboxs_suffix"]; 
		else
			echo $GLOBALS['basemodule_url'] . "/" . $options["comboboxs_suffix"]; 
?>"><?php echo (($models["selected_relation"] != null)?$models["selected_relation"]->getData()->name:t("Projects")) ?> <i>more</i></a>

			<ul class="maindropdown-2-noscroll" onclick="desactivate_menu1();">



				<?php
				$current_relation_cat = "";
				for ($i = 0; $i < count($data["relationlist"]); $i++) : ?>

				<?php if($i == 0 || $current_relation_cat != $data["relationlist"][$i]->relation_cat_name) : ?>
					<li><a href="#"><?php echo $data["relationlist"][$i]->relation_cat_name; ?> <span>&raquo;</span></a>
						<ul class="maindropdown-3">					
				<?php endif;
					$current_relation_cat = $data["relationlist"][$i]->relation_cat_name; ?>

					<li><a href="<?php echo $GLOBALS['basemodule_url'] . "/" . 
						$data["relationlist"][$i]->url_name . "/" . $options["comboboxs_suffix"];?>">
						<?php echo $data["relationlist"][$i]->relation_name; ?></a></li>

				<?php if($i == count($data["relationlist"]) - 1 ||
					 $current_relation_cat != $data["relationlist"][$i+1]->relation_cat_name) : ?>
					</ul></li>
				<?php endif; ?>

				<?php endfor; ?> 
			</ul>
		</li>

<?php endif; ?>

<?php
	//Show only the category dropdown list if we have categories in the database. Otherwise it's no use!
	if($models["selected_relation"] == null && count($data["categorylist"]) > 0 ||
		$models["selected_relation"] != null && count($data["relationsubcategorylist"]) > 0) : 
?>

		<li class="maindropdown-1drop <?php echo $category_selected; ?>" id="maindropdown-menu2"> 
			<?php
			if($models["selected_relation"] != null && $models["selected_relationsubcat"] != null)
				echo "<a id=\"maindropdown-2link\" onclick=\"activate_menu2();return false;\" href=\"" . $GLOBALS['basemodule_url'] . "/" . 
					$models["selected_relation"]->getData()->url_name .
					"/" . $models["selected_relationsubcat"]->getData()->url_name . "/" . $options["comboboxs_suffix"]
					 . "\">" . $models["selected_relationsubcat"]->getData()->name;
			else if($models["selected_relation"] != null && $models["selected_relationsubcat"] == null)
				echo "<a id=\"maindropdown-2link\" onclick=\"activate_menu2();return false;\" href=\"" . $GLOBALS['basemodule_url'] . "/" . 
					$models["selected_relation"]->getData()->url_name .
					"/" . $options["comboboxs_suffix"] . "\">" . t("Project categories");
			else if($models['selected_category'] != null)
				echo "<a id=\"maindropdown-2link\" onclick=\"activate_menu2();return false;\" href=\"" . $GLOBALS['basemodule_url'] . "/" . 
					$models['selected_category']->getData()->url_name . "/" . 
					$options["comboboxs_suffix"] . "\">" . $models["selected_category"]->getData()->name;
			else
				echo "<a id=\"maindropdown-2link\" onclick=\"activate_menu2();return false;\" href=\"" . $GLOBALS['basemodule_url'] . "/" . 
					$options["comboboxs_suffix"] . "\">" . t("Global categories");
			?> <i class="maindropdown-1right">more</i></a>
			<ul class="maindropdown-2">
				<?php if($models["selected_relation"] != null) : ?>
					
					<li><a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $models["selected_relation"]->getData()->url_name . 
						"/" . $options["comboboxs_suffix"]; ?>"><?php echo t('All categories'); ?></a></li>

					<?php foreach($data["relationsubcategorylist"] as $relationsubcat) : ?>
					<li><a href="<?php echo $GLOBALS['basemodule_url'] . "/" . $models["selected_relation"]->getData()->url_name .
						"/" . $relationsubcat->url_name . "/" . $options["comboboxs_suffix"];?>">
						<?php echo $relationsubcat->name; ?></a></li>
					<?php endforeach; ?> 

				<?php else : ?>


					<?php if(is_array($data["categorylist"])) : ?>
						<?php foreach($data["categorylist"] as $category) : ?>
						<li><a href="<?php echo $GLOBALS['basemodule_url'] . "/" . 
							$category->url_name . "/" . $options["comboboxs_suffix"];?>">
							<?php echo $category->name; ?></a></li>
						<?php endforeach; ?>
					<?php endif; ?>

				<?php endif; ?>
			</ul>
		</li>

<?php endif; ?>

	</ul>

</td></tr>




<tr style="background:#eaeada;"><td colspan="6">
<div style="margin-top:3px"></div>
</td></tr>
<tr>
<td class="ubuntu_roundnavbar_main" style="padding-left:3px; padding-top:4px"></td>
<?php if(QAPollConfig::getInstance()->getValue("choice_number_approvals_needed") > 0) : ?>
<td class="ubuntu_roundnavbar_main" style="padding:0px">

	<?php if($options["selected_entry"] == "ingestation") : ?>
	<b class="ubuntu_title">
	<b class="ubuntu_title1"><b></b></b>
	<b class="ubuntu_title2"><b></b></b>
	<b class="ubuntu_title3"></b>
	<b class="ubuntu_title4"></b>
	<b class="ubuntu_title5"></b></b>
	<?php endif; ?>

</td>
<?php endif; ?>
<td class="ubuntu_roundnavbar_main" style="padding:0px">

	<?php if($options["selected_entry"] == "popular") : ?>
	<b class="ubuntu_title">
	<b class="ubuntu_title1"><b></b></b>
	<b class="ubuntu_title2"><b></b></b>
	<b class="ubuntu_title3"></b>
	<b class="ubuntu_title4"></b>
	<b class="ubuntu_title5"></b></b>
	<?php endif; ?>

</td>
<td class="ubuntu_roundnavbar_main" style="padding:0px">

	<?php if($options["selected_entry"] == "indev") : ?>
	<b class="ubuntu_title">
	<b class="ubuntu_title1"><b></b></b>
	<b class="ubuntu_title2"><b></b></b>
	<b class="ubuntu_title3"></b>
	<b class="ubuntu_title4"></b>
	<b class="ubuntu_title5"></b></b>
	<?php endif; ?>

</td>
<td class="ubuntu_roundnavbar_main" style="padding:0px">

	<?php if($options["selected_entry"] == "implemented") : ?>
	<b class="ubuntu_title">
	<b class="ubuntu_title1"><b></b></b>
	<b class="ubuntu_title2"><b></b></b>
	<b class="ubuntu_title3"></b>
	<b class="ubuntu_title4"></b>
	<b class="ubuntu_title5"></b></b>
	<?php endif; ?>

</td>
<td class="ubuntu_roundnavbar_main" style="padding-left:3px"></td>
</tr>

<tr>
<td class="ubuntu_roundnavbar_main" style="padding-left:3px"></td>
<?php if(QAPollConfig::getInstance()->getValue("choice_number_approvals_needed") > 0) : ?>

<td class="<?php echo ($options["selected_entry"] == "ingestation")?"ubuntu_title_main":"ubuntu_roundnavbar_main"; ?>" style="width:25%">


	<table style="margin: 0px 0px 0px 10px"><tr><td>
	<a href="<?php echo $options["biglinks_prefix"] . "ideas_in_preparation/" . $options["biglinks_suffix"]; ?>">
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/frontpage-biohazard3.png" alt="<?php echo t('Idea sandbox'); ?>">
	</a>
	</td><td>
	
	<a href="<?php echo $options["biglinks_prefix"] . "ideas_in_preparation/" . $options["biglinks_suffix"]; ?>" class="biglink" style="font-weight:bold">
	<span style="font-size:17px; padding-left:2px; color: #5A3320"> <?php echo t('Idea sandbox'); ?></span>
	</a>
	
	</td></tr></table> 


</td>

<?php endif; ?>

<td class="<?php echo ($options["selected_entry"] == "popular")?"ubuntu_title_main":"ubuntu_roundnavbar_main"; ?>" style="width:<?php echo((QAPollConfig::getInstance()->getValue("choice_number_approvals_needed") > 0)?"25":"33"); ?>%">


	<table style="margin: 0px 0px 0px 10px"><tr><td>
	<a href="<?php echo $options["biglinks_prefix"] . $options["biglinks_suffix"]; ?>">
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/frontpage-thunder.png" alt="<?php echo t('Popular ideas'); ?>">
	</a>
	</td><td>
	<a href="<?php echo $options["biglinks_prefix"] . $options["biglinks_suffix"]; ?>" class="biglink" style="font-weight:bold">
	<span style="font-size:17px; padding-left:2px; color: #5A3320"> <?php echo t('Popular ideas'); ?></span>
	</a>
	</td></tr></table> 


</td><td class="<?php echo ($options["selected_entry"] == "indev")?"ubuntu_title_main":"ubuntu_roundnavbar_main"; ?>" style="width:<?php echo((QAPollConfig::getInstance()->getValue("choice_number_approvals_needed") > 0)?"25":"33"); ?>%">


	<table style="margin: 0px 0px 0px 10px"><tr><td>
	<a href="<?php echo $options["biglinks_prefix"] . "ideas_in_development/" . $options["biglinks_suffix"]; ?>">
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/frontpage-inprogress.png" alt="<?php echo t('Ideas in development'); ?>">
	</a>
	</td><td>
	<a href="<?php echo $options["biglinks_prefix"] . "ideas_in_development/" . $options["biglinks_suffix"]; ?>" class="biglink" style="font-weight:bold">
	<span style="font-size:17px; padding-left:2px; color: #5A3320"> <?php echo t('Ideas in development'); ?></span>
	</a>
	</td></tr></table> 


</td><td class="<?php echo ($options["selected_entry"] == "implemented")?"ubuntu_title_main":"ubuntu_roundnavbar_main"; ?>" style="width:<?php echo((QAPollConfig::getInstance()->getValue("choice_number_approvals_needed") > 0)?"25":"33"); ?>%">


	<table style="margin: 0px 0px 0px 10px"><tr><td>
	<a href="<?php echo $options["biglinks_prefix"] . "implemented_ideas/" . $options["biglinks_suffix"]; ?>">
	<img src="<?php echo "/" . $this->getThemePath(); ?>/images/frontpage-implemented.png" alt="<?php echo t('Implemented ideas'); ?>">
	</a>
	</td><td>
	<a href="<?php echo $options["biglinks_prefix"] . "implemented_ideas/" . $options["biglinks_suffix"]; ?>" class="biglink" style="font-weight:bold">
	<span style="font-size:17px; padding-left:2px; color: #5A3320"> <?php echo t('Implemented ideas'); ?></span>
	</a>
	</td></tr></table> 


</td>
<td class="ubuntu_roundnavbar_main" style="padding-left:3px"></td>
</tr>

<?php if($options["title"] != "") : ?>
	<tr><td class="ubuntu_title_main" colspan="<?php echo((QAPollConfig::getInstance()->getValue("choice_number_approvals_needed") > 0)?"6":"5"); ?>">
	<div style="padding:10px 0px 0px 10px; margin: 0px 0px 5px 0px; font-size:22px;color: #5a3320; font-weight:bold">
	<?php echo $options["title"]; ?>
	</div>
	</td></tr>
<?php endif; ?>

</table>

<?php if($options["selected_entry"] == "ingestation" || $options["selected_entry"] == "popular" || $options["selected_entry"] == "indev" || $options["selected_entry"] == "implemented") : ?>

<b class="ubuntu_title">
<b class="ubuntu_title5"></b>
<b class="ubuntu_title4"></b>
<b class="ubuntu_title3"></b>
<b class="ubuntu_title2"><b></b></b>
<b class="ubuntu_title1"><b></b></b></b>

<?php else : ?>

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>

<?php endif; ?>

