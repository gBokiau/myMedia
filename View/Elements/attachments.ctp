<?php
/**
 * Attachments Element File
 *
 * Element listing associated attachments of the view's model
 * Add, delete (detach) an Attachment
 *
 * Copyright (c) 2007-2010 David Persson
 *
 * Distributed under the terms of the MIT License.
 * Redistributions of files must retain the above copyright notice.
 *
 * PHP version 5
 * CakePHP version 1.2
 *
 * @package    media
 * @subpackage media.views.elements
 * @copyright  2007-2010 David Persson <davidpersson@gmx.de>
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://github.com/davidpersson/media
 */

$count =
	(isset($this->request->data[$assocAlias])) ?
	count($this->request->data[$assocAlias]) : 0;
?>
<fieldset class="tabbed">
	<legend><?= Inflector::pluralize($assocAlias);?></legend>
	<div id="demo-status" class="hide">
		<p>
			<a href="#" id="demo-browse">Browse Files</a> |
			<a href="#" id="demo-clear">Clear List</a> |
			<a href="#" id="demo-upload">Start Upload</a>
		</p>
		<div>
			<strong class="overall-title"></strong><br />
			<?php echo $this->Html->image("/mymedia/img/progress-bar/bar.gif", array("class"=>"progress overall-progress"));?>
		</div>
		<div>
			<strong class="current-title"></strong><br />
			<?php echo $this->Html->image("/mymedia/img/progress-bar/bar.gif", array("class"=>"progress current-progress"));?>
		</div>
		<div class="current-text"></div>
	</div>

	<ul id="demo-list"></ul>
	<div class="attachments element">
		<!-- New Attachment -->
		<div class="new">
		<?php
			echo $this->Superform->hide(array(
				'model'=>$model,
				'group'=>strtolower($assocAlias),
				'sort'=>$count
				), array('prefix'=>$assocAlias . '.' . $count));
			/*echo $form->hidden($assocAlias . '.0.model', array('value' => $model));
			echo $form->hidden($assocAlias . '.0.group', array('value' => strtolower($assocAlias)));
			echo $form->hidden($assocAlias . '.0.sort', array('value' => $count));*/
			echo $this->Form->input($assocAlias . '.' . $count .'.file', array(
				'label' => __('File', true),
				'type'  => 'file',
				'error' => array(
					'error'      => __('An error occured while transferring the file.', true),
					'resource'   => __('The file is invalid.', true),
					'access'     => __('The file cannot be processed.', true),
					'location'   => __('The file cannot be transferred from or to location.', true),
					'permission' => __('Executable files cannot be uploaded.', true),
					'size'       => __('The file is too heavy.', true),
					'pixels'     => __('The file is too large.', true),
					'extension'  => __('The file has the wrong extension.', true),
					'mimeType'   => __('The file has the wrong MIME type.', true),
			)));
			echo $this->Form->input($assocAlias . '.' . $count .'.alternative', array(
				'label' => __('Textual replacement', true),
				'value' => '',
				'error' => __('A textual replacement must be provided.', true)
			));
		?>
		</div>
		<!-- Existing Attachments -->
		<div class="existing" id="existing">
		<?php
		if (isset($this->request->data[$assocAlias]) && count($this->request->data[$assocAlias])): ?>
			<?php foreach($this->request->data[$assocAlias] as $i=>$item) :?>
			<div id="<?php echo $assocAlias.$i;?>"<?php if(array_key_exists('delete', $item) && $item['delete']) echo ' class="dodelete"';?>>
				<?php if (!isset($this->request->data[$assocAlias][$i])) {
					echo 'Error';
					if (!isset($errorHasOccured)) {
						$errorHasOccured = 1;
						$this->log(array($assocAlias, $this->data));
					}
				}?>
			<?php echo $this->element($element, array(
						'assocAlias'=>$assocAlias,
						'previewVersion'=>$previewVersion,
						'i'=>$i,
						'model'=>$model,
						'item'=>$item)
			);?>
			</div>
			<?php endforeach; ?>
		<?php endif ?>
		</div>
	</div>
</fieldset>