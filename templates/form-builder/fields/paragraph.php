<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 12/10/2018
 * Time: 10:48 AM
 */
?>
<div class="fat-popup-field <?php echo (isset($field->dataWidth) ? $field->dataWidth : ''); ?>">
    <?php echo isset($field->label) ? html_entity_decode($field->label) : '' ; ?>
</div>
