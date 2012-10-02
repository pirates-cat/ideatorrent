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

 
//Create the img  
$image = @imagecreatefrompng("modules/ideatorrent/themes/" . QAPollConfig::getInstance()->getValue("selected_theme") .
	"/imagelink/img_idea_little.png") or die ("Internal error #1");  

$verafont = "/usr/share/fonts/truetype/ttf-bitstream-vera/Vera.ttf";
$veraboldfont = "/usr/share/fonts/truetype/ttf-bitstream-vera/VeraBd.ttf";


$white = ImageColorAllocate ($image, 255, 255, 255); 

//Handle the title
$title = strip_tags_and_evil_attributes($data->title);

//If title lenght > 25, separate the title into two lines or even three lines
if(strlen($title) > 45)
{
	$line1size = 25;
	$line2size = 25;
}
else
{
	$line1size = 20;
	$line2size = 20;
}

if(strlen($title) > $line1size)
{
	$space_index = strpos($title, " ", $line1size);
	if($space_index !== false)
	{
		$title2 = substr($title, $space_index + 1);
		$title = substr($title, 0, $space_index);
	}
	if(strlen($title2) > $line2size)
	{
		$space_index = strpos($title2, " ", $line2size);
		if($space_index !== false)
		{
			$title3 = substr($title2, $space_index + 1);
			$title2 = substr($title2, 0, $space_index);
		}
	}
}

//Now we compute the optimal font size: as long as it does not fit in the image, reduce the font size.
for($size = 15; $size > 1; $size--)
{
	list($lx,$ly,$rx,$ry, $ulx,$uly,$urx,$ury) = imageftbbox($size, 0, $verafont, $title);
	if(($rx - $lx) + 43 <= 150)
		break;
}

//Force low font for three lines
if(isset($title2) && isset($title3))
	$size = 6;

//Write the title, and optionally the second line and third line.
if(isset($title2) && isset($title3))
	$margin = 7;
else if(isset($title2))
	$margin = 12;
else
	$margin = 16;

imagettftext($image, $size, 0, 43, ($ly - $uly) + $margin, $white, $verafont, $title);
if(isset($title2))
{
	imagettftext($image, $size, 0, 43, ($ly - $uly)*2 + 1 + $margin, $white, $verafont, $title2);
	if(isset($title3))
		imagettftext($image, $size, 0, 43, ($ly - $uly)*3 + 2 + $margin, $white, $verafont, $title3);
}



//We finally create the img
ImagePng ($image);
?> 
