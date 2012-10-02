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

/**
 * This class takes care of sending mail.
 */
class QAPollMailSender
{
	/**
	 * The instance of the logger.
	 */
	static private $_instance = null;

	/**
	 * Get the unique instance.
	 */
	static function getInstance()
	{
		if (self::$_instance == null)
			self::$_instance = new QAPollMailSender;

		return self::$_instance;
	}

	/**
	 * Send a message.
	 */
	function sendMessage($sender, $destination, $replyto, $title, $message)
	{
		//echo "Sender: $sender<br/>";
		//echo "Destination: $destination<br/>";
		//echo "Reply-to: $replyto<br/>";
		//echo "Title: $title<br/>";
		//echo "Message: $message<br/>";
		return mail($destination, $title, wordwrap($message, 70),
			"From: " . $sender . "\n" . 
			"Content-Type: text/plain; charset=utf-8;" . 
			"Reply-To: " . $replyto);
	}

	/**
	 * Send a preformatted message. The template are located in the mailsender_tmpl/ folder.
	 */
	function sendPreformattedMessage($sender, $destination, $replyto, $template_name, $args)
	{
		//Set some defaults arg names.
		$args['site_name'] = $GLOBALS['entrypoint']->_data->title;

		//Capture the message title into a buffer
		ob_start();
		include "mailsender_tmpl/" . $template_name . "_title.php";
		$title = ob_get_contents();
		ob_end_clean();

		//Capture the message into a buffer
		ob_start();
		include "mailsender_tmpl/" . $template_name . ".php";
		$body = ob_get_contents();
		ob_end_clean();

		//Send the message
		return $this->sendMessage($sender, $destination, $replyto, $title, $body);
	}

}






?>
