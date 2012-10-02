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



class RelationListJSONView extends View
{
	/**
	 * Prepare and display the page.
	 */
	function display($template = "json")
	{
		global $site;
		$content = "";

		//Execute default template
		$content .= $this->loadNonThemedTemplate("relationlist/", $template);

		//Don't return the content, otherwise drupal will add some HTML in the way.
		echo $content;

		return null;
	}


}

?>
