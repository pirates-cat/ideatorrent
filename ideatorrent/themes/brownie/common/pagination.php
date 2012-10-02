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


//Compute the number of pages.
$num_of_pages = ceil($data["rowCount"] / $data["numberRowsPerPage"]);

//Sanitize data
if($data["page"] < 1)
	$data["page"] = 1;
if($data["page"] > $num_of_pages)
	$data["page"] = $num_of_pages;

function qapoll_ubuntu_brainstorm_link($pageNb, $data)
{
	return $data["url_prefix"] . $pageNb .
		(($data["numberRowsPerPage"] != $GLOBALS['site']->getSetting("default_number_item_per_page"))?$data["url_middlefix"] . $data["numberRowsPerPage"]:"") . $data["url_suffix"];
}


$output = "";

//If only one page, do not show anything
if($data["rowCount"] <= $data["numberRowsPerPage"])
	return $output;

if($data["page"] > 1)
	$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link($data["page"] - 1, $data) . "\">&lt;&lt; " . t("Previous") . "</a>&nbsp;";
if($data["page"] == 1)
	$output .= "1&nbsp;";
else
	$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link(1, $data) . "\">1</a>&nbsp;";
if($data["page"] == 2)
	$output .= "2&nbsp;";
else
	$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link(2, $data) . "\">2</a>&nbsp;";

if($data["page"] <= 8)
{
	for($i = 3; $i <= 9 && $i <= $num_of_pages; $i++)
	{
		if($data["page"] == $i)
			$output .= "$i&nbsp;";
		else
			$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link($i, $data) . "\">$i</a>&nbsp;";
	}
}
else if($data["page"] < $num_of_pages - 7)
{
	$output .= "...&nbsp";
	for($i = $data["page"] - 2; $i <= $data["page"] + 2; $i ++)
	{
		if($data["page"] == $i)
			$output .= "$i&nbsp;";
		else
			$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link($i, $data) . "\">$i</a>&nbsp;";
	}
}
if($num_of_pages > 9)
{
	$output .= "...&nbsp;";
}
if($data["page"] >= $num_of_pages - 7 && $data["page"] > 8)
{
	for($i = $num_of_pages - 8; $i <= $num_of_pages - 2 ; $i++)
	{
		if($data["page"] == $i)
			$output .= "$i&nbsp;";
		else
			$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link($i, $data) . "\">$i</a>&nbsp;";
	}
}

if($data["page"] > 8)
{
	if($data["page"] == ($num_of_pages - 1))
		$output .= ($num_of_pages - 1) . "&nbsp;";
	else
		$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link($num_of_pages - 1, $data) . "\">" .
			($num_of_pages - 1) . "</a>&nbsp;";

	if($data["page"] == $num_of_pages)
		$output .= $num_of_pages . "&nbsp;";
	else
		$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link($num_of_pages, $data) . "\">" .
			$num_of_pages . "</a>&nbsp;";
}

if($data["page"] < $num_of_pages)
	$output .= "<a class=\"navlink\" href=\"" . qapoll_ubuntu_brainstorm_link($data["page"] + 1, $data) . "\">" . t("Next") . " &gt;&gt;</a>&nbsp;";

echo $output;



?>
