<?php
	$prefix = $assocAlias . '.' . $i;
	echo $this->Form->hidden($prefix.'.model', array('value' => $model));
	echo $this->SuperForm->hide($item, array('prefix'=>$prefix, 'blacklist'=>array('model', 'status', 'delete', 'foreign_key', 'checksum', 'created', 'modified', 'file')));
	if ($file = $this->Media->file("$previewVersion/img/{$item['basename']}")) {
		echo $this->Media->embed($file, array(
			'restrict' => array('image')
		));

 		/*$Media = Media::factory($file);
		$size = $this->Media->size($file);

		if (isset($this->Number)) {
			$size = $this->Number->toReadableSize($size);
		} else {
			$size .= ' Bytes';
		}

		printf('<span>%s&nbsp;(%s/%s) <em>%s</em></span>',
				$url ? $this->Html->link($item['basename'], $url) : $item['basename'],
				strtolower($Media->name), $size, $item['alternative']);*/
	}

	echo $this->Form->input($prefix.'.delete', array(
		'label' => __('Supprimer', true),
		'type' => 'checkbox',
		'value' => 0
	));
	echo $this->Form->input($prefix . '.status', array(
		'label' => __('Masquer', true),
		'value' => 0,
		'checked' => ($item['status'] == '1')
	));
	echo $this->Form->input($prefix . '.homepage', array(
		'label' => __('Homepage', true),
		'value' => 0,
		'checked' => ($item['homepage'] == '1')
	));
?>