<?php
//$data =& $_MIDCOM->get_custom_context_data('request_data');
?>
<div class="area">
    <h2><?php echo $data['l10n']->get("contacts"); ?></h2>
    <?php $data['members_qb']->show_pages(); ?>
    <dl>