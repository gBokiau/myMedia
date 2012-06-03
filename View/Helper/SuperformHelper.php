<?php
App::uses('AppHelper', 'View/Helper');

class SuperformHelper extends AppHelper {
    public $helpers = array('Form');

    public function __construct(View $view, $settings = array()) {
        parent::__construct($view, $settings);
    }

    function hide($source = array(), $options = array()) {
		$out = '';
		$prefix = (array_key_exists('prefix', $options)) ? $options['prefix'].'.' : '';
		$exclude = (array_key_exists('blacklist', $options)) ? (array) $options['blacklist'] : array();

		foreach ($source as $field => $value) {
			if(!in_array($field, $exclude))
				$out .= $this->Form->hidden($prefix . $field, array('value' => $value))."\n";
		}
        return $out;
    }
}
?>