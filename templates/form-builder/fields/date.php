<?php
/**
 * Created by PhpStorm.
 * User: RoninWP
 * Date: 11/30/2018
 * Time: 3:21 PM
 */
$date_format = get_option('date_format');
$date_format = str_replace('M', 'MMM',$date_format);
$date_format = str_replace('F', 'MM',$date_format);
$date_format = str_replace('m', 'mm', $date_format);
$date_format = str_replace('n', 'm', $date_format);

$date_format = str_replace('d', 'dd', $date_format);
$date_format = str_replace('j', 'd', $date_format);
$date_format = str_replace('s', '', $date_format);
$date_format = str_replace('y', 'yy', $date_format);
$date_format = str_replace('Y', 'yyyy', $date_format);
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
            <input type="text" <?php echo (isset($field->required) && $field->required ? 'required' : ''); ?>
                   value="<?php echo (isset($field->value) ? $field->value : ''); ?>"
                   placeholder="<?php echo (isset($field->placeholder) ? $field->placeholder : ''); ?>"
                   name="<?php echo (isset($field->name) ? $field->name : ''); ?>"
                   data-label="<?php echo isset($field->label) && $field->label ? $field->label : $field->name; ?>"
                   id="<?php echo (isset($field->name) ? $field->name : ''); ?>"
                   data-locale="<?php echo (isset($field->dataLocale) && $field->dataLocale ? $field->dataLocale : 'en') ;?>"
                   data-date-format="<?php echo esc_attr($date_format);?>"
                   class="fat-sb-field-builder fat-sb-date-field"
            >
        </div>
        <div class="field-error-message">
            <?php echo (isset($field->dataErrorMessage) ? $field->dataErrorMessage : ''); ?>
        </div>

    </div>
</div>