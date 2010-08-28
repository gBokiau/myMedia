<?php
/* /app/views/helpers/link.php (using other helpers) */
class SuperFormHelper extends AppHelper {
    var $helpers = array('Form');

    function hide($source = array(), $options = array()) {
		$out = '';
		$prefix = (array_key_exists('prefix', $options)) ? $options['prefix'].'.' : '';
		$exclude = (array_key_exists('blacklist', $options)) ? (array) $options['blacklist'] : array();

		foreach ($source as $field => $value) {
			if(!in_array($field, $exclude))
				$out .= $this->Form->hidden($prefix . $field, array('value' => $value))."\n";
		}
        return $this->output($out);
    }
}
?>