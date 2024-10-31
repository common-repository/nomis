<?php

function nomisFormAdmin($formDbName)
{
	$htmlSelected = 'selected="selected" ';
	?>
		<div class="form-fields">
			<?php
			foreach (get_option($formDbName) as $fieldName => $fieldOptions):
				if ($fieldOptions['options']['type'] == 'select')
				{
					$selectOptions = array();
					foreach ($fieldOptions['options']['options'] as $selectOption => $selectLabel)
					{
						$selectOptions[] = $selectOption . '=>' . $selectLabel;
					}
					$fieldName = $fieldName . '::' . implode(',', $selectOptions);
				}
			?>
			<div class="form-field">
				<label><?php _e('Name', 'nomis'); ?></label>
				<input class="name" type="text" name="<?php echo $formDbName; ?>_name[]" value="<?php echo $fieldName; ?>" />
				<label><?php _e('Label', 'nomis'); ?></label>
				<input class="label" type="text" name="<?php echo $formDbName; ?>_label[]" value="<?php echo htmlentities($fieldOptions['options']['label'], ENT_QUOTES, 'utf-8'); ?>" />
				<div class="clear"></div>
				<select name="<?php echo $formDbName; ?>_type[]" class="type">
					<option value="text" <?php if ($fieldOptions['options']['type'] == 'text') echo $htmlSelected; ?>>Text</option>
					<option value="select" <?php if ($fieldOptions['options']['type'] == 'select') echo $htmlSelected; ?>>Select</option>
					<option value="textarea" <?php if ($fieldOptions['options']['type'] == 'textarea') echo $htmlSelected; ?>>Textarea</option>
					<option value="checkbox" <?php if ($fieldOptions['options']['type'] == 'checkbox') echo $htmlSelected; ?>>Checkbox</option>
				</select>
				<select name="<?php echo $formDbName; ?>_required[]" class="required">
					<option value="1" <?php if ($fieldOptions['options']['required'] == true) echo $htmlSelected; ?>><?php _e('Required', 'nomis'); ?></option>
					<option value="0" <?php if ($fieldOptions['options']['required'] != true) echo $htmlSelected; ?>><?php _e('Not required', 'nomis'); ?></option>
				</select>
				<label><?php _e('Description', 'nomis'); ?></label>
				<input class="description" type="text" name="<?php echo $formDbName; ?>_description[]" value="<?php echo htmlentities($fieldOptions['options']['description'], ENT_QUOTES, 'utf-8'); ?>" />
				<span class="ui-icon ui-icon-closethick remove-form-field"></span>
				<div class="clear"></div>
			</div>
			<a href="javascript:;" class="new-form-field"><?php _e('New form field', 'nomis'); ?></a>
			<?php
			endforeach;
			?>
		</div>

	<?php
}