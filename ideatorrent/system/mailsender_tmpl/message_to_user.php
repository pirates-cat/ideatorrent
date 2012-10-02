Hi,

The user <?php echo $args['sender_name']; ?> from <?php echo $args['site_name']; ?> sent you a message.

------------------

<?php echo strip_tags_and_evil_attributes($args['message']); ?>


------------------

To reply to this message, use <?php echo $args['sender_name']; ?>'s email address: <?php echo $args['sender_mail']; ?>


Don't want to receive user messages from <?php echo $args['site_name']; ?> anymore? Go to your profile at <?php echo $args['site_name']; ?> and uncheck the relevant option.

Best regards,
The <?php echo $args['site_name']; ?> team.
