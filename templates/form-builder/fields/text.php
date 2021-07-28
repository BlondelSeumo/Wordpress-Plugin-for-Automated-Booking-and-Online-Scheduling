<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 11/30/2018
 * Time: 3:21 PM
 */
?>
<div class="one fields">
    <div class="field">
        <?php if(isset($field->label) && $field->label): ?>
            <label for="<?php echo (isset($field->name) ? $field->name : ''); ?>">
                <?php echo esc_html($field->label);?>
                <?php if(isset($field->required) && $field->required): ?>
                    <span style="color: red"> *</span>
                <?php endif; ?>
                <?php if(isset($field->description) && $field->description): ?>
                    <div class="ui icon ui-tooltip" data-position="top left"
                         data-content="<?php echo esc_attr($field->description); ?>">
                        <i class="question circle icon"></i>
                    </div>
                <?php endif;?>
            </label>
        <?php endif; ?>
        <div class="ui left input">
            <input type="<?php echo (isset($field->subtype) ? $field->subtype : 'text'); ?>" <?php echo (isset($field->required) && $field->required ? 'required' : ''); ?>
                   value="<?php echo (isset($field->value) ? $field->value : ''); ?>"
                   placeholder="<?php echo (isset($field->placeholder) ? $field->placeholder : ''); ?>"
                   name="<?php echo (isset($field->name) ? $field->name : ''); ?>"
                   data-label="<?php echo isset($field->label) && $field->label ? $field->label : $field->name; ?>"
                   id="<?php echo (isset($field->name) ? $field->name : ''); ?>"
                   data-onchange="<?php echo esc_attr($onChange);?>"
                   class="fat-sb-field-builder">
        </div>
        <div class="field-error-message">
            <?php echo (isset($field->dataErrorMessage) ? $field->dataErrorMessage : ''); ?>
        </div>

    </div>

</div>
