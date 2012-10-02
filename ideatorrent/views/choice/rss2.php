<?php

/*
Copyright (C) 2008 Dean Sas <dean@deansas.org>

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

echo "<?xml version=\"1.0\"?>"; ?>

<rss version="2.0">
  <channel>
    <title><![CDATA[<?php echo strip_tags_and_evil_attributes($this->_data->title); ?>]]></title>
    <link><?php echo $GLOBALS['basemodule_url'] . "/item/" . $this->_data->id . "/"; ?></link>
    <description><![CDATA[<?php echo str_replace("\n", "<br />", strip_tags_and_evil_attributes($this->_data->description, $this->getThemeSetting("item_description_auth_tags"))) ?>
<br />
<br />
<?php for ($i = 0; $i < count($this->_data->solutions); $i++) : ?>

<?php
$this->solution =& $this->_data->solutions[$i];
echo $this->loadNonThemedTemplate("choicelist/", "rss2", "solution");
?>

<?php endfor; ?>


]]></description>

    <language>en-us</language>
    <pubDate><?php echo date("r", strtotime($this->_data->date))?></pubDate>
    <lastBuildDate><?php echo date("r", strtotime($this->_data->last_comment_date))?></lastBuildDate>
    <generator>QAPoll module</generator>
    <guid isPermaLink="true"><?php echo $GLOBALS['basemodule_url'] . "/idea/" . $this->_data->id . "/"; ?></guid>
    <?php foreach($this->_data->comment_items as $comment): ?>
    <?php 
     $this->comment =& $comment;
     echo $this->loadNonThemedTemplate("choice/", "rss2", "comment");
    ?>
    <?php endforeach; ?>
  </channel>
</rss>
