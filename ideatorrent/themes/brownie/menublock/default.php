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

<!-- QAPoll right block starts here -->
<div class="qapoll">

<?php if($_GET['keywords'] != null || $_GET['tags'] != null || $_GET['admintags'] != null) : ?>

<b class="red_title">
<b class="red_title1"><b></b></b>
<b class="red_title2"><b></b></b>
<b class="red_title3"></b>
<b class="red_title4"></b>
<b class="red_title5"></b></b> 

<?php else : ?>

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 

<?php endif; ?>


<div class="<?php 
	if($_GET['keywords'] != null || $_GET['tags'] != null || $_GET['admintags'] != null) 
		echo "red_title_main"; 
	else 
		echo "ubuntu_roundnavbar_main"; ?>"
 style="padding:2px 5px 0px 5px">


<table style="width:100%">

<?php if($_GET['keywords'] != null || $_GET['tags'] != null || $_GET['admintags'] != null) : ?>
<tr><td style="padding-top:0px; padding-bottom:4px">

<form method="GET" action="">
<table style="width:100%">
<tr><td style="padding:0px 0px 0px 0px">
	<span style="font-size:15px"><b><?php echo t('Filter in use'); ?></b></span>
</td><td style="padding:0px 0px 0px 0px">
	<input type="submit" value="Clear" style="margin-left:10px; width:50px; font-weight:bold; font-size:11px; vertical-align:top; float:right"/>
</td></tr>
<tr><td colspan="2" style="padding:1px 0px 0px 0px">
	<div style="border-bottom: 1px solid rgb(160, 160, 160); width: 100%;"></div>
</td></tr>
</table>
</form>

</td></tr>
<?php endif; ?>


<tr><td>


<form method="GET" action="
<?php
	//Okayyyy
	//If we are in an item listing, that's fine, we use action=""
	//But otherwise we will have to redirect to an idea listing
	//And here begins the fun!

	//Get the controller instance
	$controller = QAPollController::getInstance();

	//Get the View object used to display the page.
	$view =& $controller->getOutputPageView();

	//If we are a ChoiceViewList, just go to the same page when filtering
	if(is_a($view, "ChoiceListView"))
		echo "";
	else
	{
		//Otherwise we will go to one of the item listings
		//if we are in an idea page, redirect to the idea listings corresponding to 
		//the idea relation/subcat/cat
		$actionpath = $GLOBALS['basemodule_url'];
		if(is_a($view, "ChoiceView") && $view->template != "submit_first_part"
			 && $view->template != "submit_second_part")
		{
			if($view->models['topnavbar_relation'] != null)
			{
				$actionpath .= "/" . $view->models['topnavbar_relation']->getData()->url_name . "/";
				if($view->models["topnavbar_relation_subcat"] != null)
					$actionpath .= $view->models["topnavbar_relation_subcat"]->getData()->url_name . "/";
			}
			else if($view->models["topnavbar_category"] != null)
				$actionpath .= "/" . $view->models["topnavbar_category"]->getData()->url_name . "/"; 
		}
		else
			$actionpath .= $GLOBALS['basemodule_prefilter_path'] . "/";

		echo $actionpath;
	}



?>">

<table style="padding:5px">
<tr><td style="padding:0px">
<div id="menufilterbox-keywordwordarea" style="padding:1px">
<span style="font-size:12px; font-weight:bold; padding-right:2px"><?php echo t('Keywords'); ?></span>
</div>
</td><td style="padding:0px">
<input id="menufilterbox-keywordinput" type="text" name="keywords" value="<?php
	 if($_GET['keywords'] != null)
		//Let's avoid XSS by removing any ", that should do it
		echo str_replace("\"", "", $_GET['keywords']);
	else
		echo t('Search...');
?>" 
<?php if($_GET['keywords'] == null) : ?>
onblur="if(this.value=='') this.value='<?php echo t('Search...'); ?>';" onfocus="if(this.value=='<?php echo t('Search...'); ?>') this.value='';" 
<?php endif; ?>
style="width:100px" />

<input type="submit" value="OK" style="margin-left:2px; width:40px; display:none" id="menufilterbox-okbutton" /><br />

</td></tr>

<tr><td colspan="2">

<div style="display:none" id="menufilterbox-advancedsearchlinkarea"><a href="#" onclick="menufilterbox_maximize(); return false;" class="blacklink2" style="font-size:12px; color: #000000"><?php echo t('Advanced search'); ?> &raquo;</a></div>

</td></tr>



<tr><td style="padding:0px">
	<div id="menufilterbox-tagsarea" style="padding:1px">
	<span style="font-size:12px; font-weight:bold; padding-right:2px"><?php echo t('Tags'); ?></span>
	</div>
</td><td style="padding:0px">
	<div id="menufilterbox-tagsarea2" style="padding:1px">
	<input id="menufilterbox-tagsinput" type="text" name="tags" value="<?php
		 if($_GET['tags'] != null)
			//Let's avoid XSS by removing any ", that should do it
			echo str_replace("\"", "", $_GET['tags']);
	?>" style="width:100px" />
	</div>
</td></tr>


<?php if(UserModel::currentUserHasPermission("search_by_admintags", "Choice", $choice)) : ?>
<tr><td style="padding:0px">
	<div  id="menufilterbox-admintagsarea" style="padding:1px">
	<span style="font-size:12px; font-weight:bold; padding-right:2px"><?php echo t('Admin tags'); ?></span>
	</div>
</td><td style="padding:0px">
<div id="menufilterbox-admintagsarea2" style="padding:1px">
	<input id="menufilterbox-admintagsinput" type="text" name="admintags" value="<?php
		 if($_GET['admintags'] != null)
			//Let's avoid XSS by removing any ", that should do it
			echo str_replace("\"", "", $_GET['admintags']);
	?>" style="width:100px" />
	</div>
</td></tr>
<?php endif; ?>

<tr><td style="text-align:center; padding-top:3px; padding-bottom:0px" colspan="2">
	<div id="menufilterbox-filterbuttonarea">
	<input type="submit" value="<?php echo t('Search'); ?>" style="width:70px"/>
	</div>
</td></tr>

</table>
</form>



</td></tr>
</table>




</div>

<?php if($_GET['keywords'] != null || $_GET['tags'] != null || $_GET['admintags'] != null) : ?>

<b class="red_title">
<b class="red_title5"></b>
<b class="red_title4"></b>
<b class="red_title3"></b>
<b class="red_title2"><b></b></b>
<b class="red_title1"><b></b></b></b>

<?php else : ?>

<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>

<?php endif; ?>

<br />





<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar1"><b></b></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar5"></b></b> 
<div class="ubuntu_roundnavbar_main" >


<table style="margin: 0px 0px 0px 8px; height:50px"><tr><td>
<a href="<?php
if(user_access($GLOBALS['site']->getData()->userrole))
	echo $GLOBALS['basemodule_url'] . $GLOBALS['basemodule_prefilter_path'] . "/submit/";
else
	echo $GLOBALS['base_url'] . "/user?destination=ideatorrent/" . substr($GLOBALS['basemodule_prefilter_path'] . "/submit/", 1);

?>" class="blacklink2" >
<img src="<?php echo "/" . $this->getThemePath(); ?>/images/ideamail.png" alt="Submit your idea">
</a>

</td><td style="padding-left:4px">
<span style="font-size:17px; font-weight:bold"><a href="<?php
if(user_access($GLOBALS['site']->getData()->userrole))
	echo $GLOBALS['basemodule_url'] . $GLOBALS['basemodule_prefilter_path'] . "/submit/";
else
	echo $GLOBALS['base_url'] . "/user?destination=ideatorrent/" . substr($GLOBALS['basemodule_prefilter_path'] . "/submit/", 1);

?>" class="blacklink2" ><?php echo t('Submit your idea'); ?></a></span>
</td></tr></table>


</div>
<b class="ubuntu_roundnavbar">
<b class="ubuntu_roundnavbar5"></b>
<b class="ubuntu_roundnavbar4"></b>
<b class="ubuntu_roundnavbar3"></b>
<b class="ubuntu_roundnavbar2"><b></b></b>
<b class="ubuntu_roundnavbar1"><b></b></b></b>




<br />


<?php if(user_access($GLOBALS['site']->getData()->userrole)) : ?>

<b class="postbutton">
<b class="postbutton1"><b></b></b>
<b class="postbutton2"><b></b></b>
<b class="postbutton3"></b>
<b class="postbutton4"></b>
<b class="postbutton5"></b></b>
<div class="postbutton_main">
<a href="<?php echo $GLOBALS['basemodule_url'] . $GLOBALS['basemodule_prefilter_path'] . "/contributor/" . $GLOBALS['user']->name . "/"; ?>" class="blacklink2" style="font-size:17px; padding-left:2px">
<?php echo t('My dashboard'); ?> &raquo;
</a>
</div>
<b class="postbutton">
<b class="postbutton5"></b>
<b class="postbutton4"></b>
<b class="postbutton3"></b>
<b class="postbutton2"><b></b></b>
<b class="postbutton1"><b></b></b></b>

<br />

<?php endif; ?>



</div>
<!-- QAPoll right block ends here -->
