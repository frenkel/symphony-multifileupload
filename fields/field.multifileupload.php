<?php

class fieldMultiFileUpload extends Field {
	public function __construct(&$parent){
		parent::__construct($parent);
		
		$this->_name = __('Multi File Upload');
		$this->_required = true;
		
		$this->set('required', 'yes');
	}

	public function displayPublishPanel(&$wrapper, $data=NULL, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL) {
		$label = Widget::Label($this->get('label'));
		$file = Widget::Input('fields' . $fieldnamePrefix . '[' . $this->get('element_name') . '][]' . $fieldnamePostfix, $data['file'], ($data['file'] ? 'hidden' : 'file'));
		$file->setAttribute('multiple', 'true');
		$label->appendChild($file);

		$wrapper->appendChild($label);
	}

	public function processRawFieldData($data, &$status, $simulate=false, $entry_id=NULL) {
		$result = '';
		for($i = 0; $i < count($data); $i++) {
			$result .= $data[$i]['name'];
		}
		return array('file' => $result);
	}
}

