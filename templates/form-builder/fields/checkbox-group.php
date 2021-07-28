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
        <?php if (isset($field->label) && $field->label): ?>
            <label for="<?php echo(isset($field->name) ? $field->name : ''); ?>">
                <?php echo esc_html($field->label); ?>
                <?php if (isset($field->required) && $field->required): ?>
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
        <div class="fat-sb-checkbox-group fat-sb-field-builder <?php echo(isset($field->inline) && $field->inline ? 'inline' : ''); ?>"
            <?php echo(isset($field->required) && $field->required ? 'required' : ''); ?>
             name="<?php echo(isset($field->name) ? $field->name : ''); ?>"
             data-label="<?php echo isset($field->label) && $field->label ? $field->label : $field->name; ?>">
            <?php if (isset($field->values) && is_array($field->values)):
                $index = 1;
                foreach ($field->values as $v) {
                    ?>
                    <div class="fat-sb-checkbox-item">
                        <input type="checkbox" name="<?php echo(isset($field->name) ? $field->name : ''); ?>[]"
                               id="<?php echo esc_attr($field->name . '_' . $index); ?>"
                               value="<?php echo esc_attr($v->value); ?>"
                        <?php echo(isset($v->selected) && $v->selected ? 'checked="checked"' : ''); ?>"
                        >
                        <label for="<?php echo esc_attr($field->name . '_' . $index); ?>"><?php echo esc_html($v->label); ?></label>
                    </div>
                    <?php
                    $index++;
                }
            endif; ?>
        </div>
        <div class="field-error-message">
            <?php echo (isset($field->dataErrorMessage) ? $field->dataErrorMessage : ''); ?>
        </div>
    </div>
</div>