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

header ("Content-type: image/png");

//Create the img  
$image = @imagecreate(420, 80) or die ("Internal error #1");  

//Prepare the colors
$white = ImageColorAllocate ($image, 255, 255, 255); 
$black = ImageColorAllocate ($image, 0, 0, 0); 
$grey = ImageColorAllocate ($image, 109, 109, 109);
$red = ImageColorAllocate ($image, 253, 108, 58);
$darkred = ImageColorAllocate ($image, 124, 20, 0);
$green = ImageColorAllocate ($image, 63, 255, 90);
$darkgreen = ImageColorAllocate ($image, 0, 83, 12);

//Compute the bar sizes
//We will use the stats of the user. Load them

$greenbarsize = (($_GET['plus'] != null)?$_GET['plus']:0) / 5;
$redbarsize = (($_GET['minus'] != null)?$_GET['minus']:0) / 5;
$biggerbarsize = ($greenbarsize > $redbarsize)?$greenbarsize:$redbarsize;
if($biggerbarsize > 200)
{
	$ratio = $biggerbarsize / 200;
	$redbarsize /= $ratio;
	$greenbarsize /= $ratio;
}

//Draw the graph
imageline($image, 9, 79, 411, 79, $grey);
imageline($image, 210, 29, 210, 79, $grey);

//For an unknown reason, imagefilledrectangle does not work on a dapper set up.
//imagefilledrectangle($image, (209 - $redbarsize), 77, 209, 39, $red);
imagefilledpolygon($image, array((209 - $redbarsize), 77, (209 - $redbarsize), 39, 209, 39, 209, 77), 4, $red);
imageline($image, (209 - $redbarsize), 77, 209, 77, $darkred);
imageline($image, (209 - $redbarsize), 39, 209, 39, $darkred);
imageline($image, (209 - $redbarsize), 39, (209 - $redbarsize), 77, $darkred);

//imagefilledrectangle($image, (211 + $greenbarsize), 77, 211, 39, $green);
imagefilledpolygon($image, array((211 + $greenbarsize), 77, (211 + $greenbarsize), 39, 211, 39, 211, 77), 4, $green);
imageline($image, (211 + $greenbarsize), 77, 211, 77, $darkgreen);
imageline($image, (211 + $greenbarsize), 39, 211, 39, $darkgreen);
imageline($image, (211 + $greenbarsize), 39, (211 + $greenbarsize), 77, $darkgreen);




//We finally create the img
ImagePng ($image);
?> 
