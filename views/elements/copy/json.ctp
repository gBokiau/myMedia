<?php
$return['html'] = $this->element('existing', array(
	'assocAlias'=>'Image',
	'previewVersion'=>'s',
	'i'=>'{i}',
	'model'=>$model,
	'item'=> $return['item']
));
echo $this->Js->object($return);
?>
