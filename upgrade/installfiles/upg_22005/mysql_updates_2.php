<?php

# Nothing of interest!

$SQL[]="ALTER TABLE ibf_posts DROP INDEX topic_id, ADD INDEX topic_id ( topic_id , queued , pid , post_date );";
$SQL[]="ALTER TABLE ibf_posts ADD INDEX post_key (post_key), ADD INDEX ip_address (ip_address);";
$SQL[]="ALTER TABLE ibf_posts ADD post_edit_reason VARCHAR(255) NOT NULL default '';";
$SQL[]="ALTER TABLE ibf_posts CHANGE post post MEDIUMTEXT NULL;";
$SQL[]="ALTER TABLE ibf_topics ADD INDEX starter_id (starter_id, forum_id, approved);";

?>