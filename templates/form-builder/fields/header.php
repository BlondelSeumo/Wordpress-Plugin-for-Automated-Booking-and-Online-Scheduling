<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 12/10/2018
 * Time: 10:48 AM
 */
?>
<?php echo sprintf('<%s class="fat-popup-field">',$field->subtype);?>
    <?php echo isset($field->label) ? html_entity_decode($field->label) : '' ; ?>
<?php echo sprintf('</%s>',$field->subtype);?>
