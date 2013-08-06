<?= form_open($form_action, '', $form_hidden); ?>
<?= validation_errors()?>

<table class="mainTable" border="0" cellspacing="0" cellpadding="0">
    <thead>
        <tr>
            <th style="width:20%"><?= lang('setting')?></th>
            <th><?= lang('value')?></th>
        </tr>
    </thead>
    <tbody>
         <tr class="even">
            <td><?= lang('key')?></td>
            <td>
            	<?php
            	$data = array('name' => 'api_key', 'value' => set_value('api_key', $api_key));
            	echo form_input($data);
            	?>
            </td>
        </tr>
    </tbody>
</table>
<?= form_submit('submit', lang('submit'), 'class="submit"')?>

<?= form_close()?>