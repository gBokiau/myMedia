<?php
class UploadComponent extends Component {
	public $settings = array(
		'action' => 'admin_fileupload',
		'render' => '/Elements/existing',
		'assocAlias'=>'Images',
		'previewVersion'=>'s');
	public $sort = 0;
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$settings = array_merge($this->settings, (array)$settings);
		parent::__construct($collection, $settings);
	}

	public function startup(Controller $controller) {
		if ($controller->request->params['action'] == $this->settings['action']) {
			$this->Controller = $controller;
			$this->request = $controller->request;
			if (method_exists($controller, 'prepareupload'))
				return $controller->prepareupload();
			return $this->prepareupload();
		}
	}

	public function prepareupload() {
		$attachmentAlias = $this->request->params['named']['group'];
		$Model = $this->_getObject();
		$Attachment = $this->_getObject($Model->name . '.' . $attachmentAlias);
		$sort = $Attachment->field('sort', array($attachmentAlias.'.foreign_key' => $this->request->data[$Model->name]['id']), $attachmentAlias.'.sort DESC');		
		$this->sort = $sort+1;
		$this->fileupload($Model, $Attachment);
	}

	public function fileupload(Model $Model, Model $Attachment) {
		$this->request->data[$Attachment->alias][0]['file'] = $_FILES['Filedata'];
		$this->request->data[$Attachment->alias][0]['sort'] = $this->sort;
		$Model->validate = array();
		$Attachment->validate = array();
		
		if ($Model->saveAll($this->request->data, array('validate' => 'first'))) {
			$Attachment->recursive = 0;
			$item = $Attachment->read(null, $Attachment->id);
			$this->request->data = $Model->read();
			$this->Controller->set(array('assocAlias'=>$this->settings['assocAlias'], 'previewVersion'=>$this->settings['previewVersion'], 'i'=>'{i}', 'model'=>$Model->name,'item'=> $item[$Attachment->alias]));
			$html = $this->Controller->render($this->settings['render'], false);
			$return = array('status'=>"1", 'html'=>$html->body());
		} else {
			$errors = $this->Controler->validateErrors($Model->name);
			$return = array('status'=>"0", 'error'=>@$errors[$Attachment->alias][0]['file']);
		}
		$this->Controller->viewClass = "Json";
		$this->Controller->set($return + array('_serialize'=>array('html', 'status')));
		echo $this->Controller->render();
		$this->_stop();
		return false;
	}
/**
 * Get the object pagination will occur on.
 *
 * @param string|Model $object The object you are looking for.
 * @return mixed The model object to paginate on.
 */
	protected function _getObject($object=null) {
		if (is_string($object)) {
			$assoc = null;
			if (strpos($object, '.') !== false) {
				list($object, $assoc) = pluginSplit($object);
			}

			if ($assoc && isset($this->Controller->{$object}->{$assoc})) {
				$object = $this->Controller->{$object}->{$assoc};
			} elseif (
				$assoc && isset($this->Controller->{$this->Controller->modelClass}) &&
				isset($this->Controller->{$this->Controller->modelClass}->{$assoc}
			)) {
				$object = $this->Controller->{$this->Controller->modelClass}->{$assoc};
			} elseif (isset($this->Controller->{$object})) {
				$object = $this->Controller->{$object};
			} elseif (
				isset($this->Controller->{$this->Controller->modelClass}) && isset($this->Controller->{$this->Controller->modelClass}->{$object}
			)) {
				$object = $this->Controller->{$this->Controller->modelClass}->{$object};
			}
		} elseif (empty($object) || $object === null) {
			if (isset($this->Controller->{$this->Controller->modelClass})) {
				$object = $this->Controller->{$this->Controller->modelClass};
			} else {
				$className = null;
				$name = $this->Controller->uses[0];
				if (strpos($this->Controller->uses[0], '.') !== false) {
					list($name, $className) = explode('.', $this->Controller->uses[0]);
				}
				if ($className) {
					$object = $this->Controller->{$className};
				} else {
					$object = $this->Controller->{$name};
				}
			}
		}
		return $object;
	}
}
?>