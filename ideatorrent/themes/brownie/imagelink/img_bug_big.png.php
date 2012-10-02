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
$image = @imagecreatefrompng("modules/qapoll/views/imagelink/tmpl/img_bug_big.png") or die ("Internal error #1");  

$verafont = "/usr/share/fonts/truetype/ttf-bitstream-vera/Vera.ttf";
$veraboldfont = "/usr/share/fonts/truetype/ttf-bitstream-vera/VeraBd.ttf";

$black = ImageColorAllocate ($image, 0, 0, 0); 

//Handle the title
$title = strip_tags_and_evil_attributes($this->_data->title);

//If title lenght > 40, separate the title into two lines
if(strlen($title) > 40)
{
	$space_index = strpos($title, " ", 40);
	if($space_index !== false)
	{
		$title2 = substr($title, $space_index + 1);
		$title = substr($title, 0, $space_index);
	}
}

//Now we compute the optimal font size: as long as it does not fit in the image, reduce the font size.
for($size = 21; $size > 1; $size--)
{
	list($lx,$ly,$rx,$ry, $ulx,$uly,$urx,$ury) = imageftbbox($size, 0, $verafont, $title);
	if(($rx - $lx) + 62 <= 290)
		break;
}

//Write the title, and optionally the second line.
imagettftext($image, $size, 0, 62, ($ly - $uly) + 24, $black, $verafont, $title);
if(isset($title2))
{
	imagettftext($image, $size, 0, 62, ($ly - $uly)*2 + 3 + 24, $black, $verafont, $title2);
}



//Handle the vote string
$red = ImageColorAllocate ($image, 177, 0, 18);
$votes = $this->_data->votes . " VOTES, ADD YOURS!";

//Now we compute the optimal font size for the vote string.
for($size = 13; $size > 1; $size--)
{
	list($lx,$ly,$rx,$ry, $ulx,$uly,$urx,$ury) = imageftbbox($size, 0, $veraboldfont, $votes);
	if(($rx - $lx) + 62 <= 290)
		break;
}

//Write the vote string.
imagettftext($image, $size, 0, 62, 72, $red, $veraboldfont, $votes);



//We finally create the img
ImagePng ($image);
?> 
