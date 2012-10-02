<?php

/*
 * Copyright (C) 2008 Dean Sas <dean@deansas.org>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * */
?>
<item>
  <title>Comment from <?php echo $this->comment->username ?></title>
  <description><![CDATA[<?php echo str_replace("\n", "<br />", strip_tags_and_evil_attributes($this->comment->comment, $this->getThemeSetting("item_comment_auth_tags"))) ?>]]></description>
  <pubDate><?php echo date("r", strtotime($this->comment->date))?></pubDate>
</item>
