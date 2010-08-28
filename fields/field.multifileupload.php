<?php

define('FILESECTIONID', 2);
define('SELECTBOXLINKID', 4);
define('FILEUPLOADID', 2);

class fieldMultiFileUpload extends Field {
	public function __construct(&$parent) {
		parent::__construct($parent);
		
		$this->_name = __('Multi File Upload');
		$this->_required = true;
		
		$this->set('required', 'yes');
	}

	public function displayPublishPanel(&$wrapper, $data=NULL, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL) {
		$label = Widget::Label($this->get('label'));
		$label->setAttribute('class', 'file');

		$file = Widget::Input('fields' . $fieldnamePrefix . '[' . $this->get('element_name') . '][]' . $fieldnamePostfix, $data['file'], 'file');
		$file->setAttribute('multiple', 'true');
		$span = new XMLElement('span');
		$span->appendChild($file);
		$label->appendChild($span);

		if($flagWithError != NULL) $wrapper->appendChild(Widget::wrapFormElementWithError($label, $flagWithError));
		else $wrapper->appendChild($label);
	}

	public function processRawFieldData($data, &$status, $simulate=false, $entry_id=NULL) {
		$uploaddir = 'workspace/' . date('Y-m-d') . '/';
		if(!is_dir(DOCROOT . '/' . $uploaddir)) {
			mkdir(DOCROOT . '/' . $uploaddir);
		}
		/* FIXME: why won't this foreach work? invalid argument when is_array($data) returns true :S 
		foreach($data as $file) {
		*/
		for($i = 0; $i < count($data); $i++) {
			/* file is an array:
			   0 -> filename
			   1 -> mimetype
			   2 -> temp filename
			   3 -> error?
			   4 -> size
		   */
			move_uploaded_file($data[$i][2], DOCROOT . '/' . $uploaddir . $data[$i][0]);

			$entry = new Entry($this);

			$entry->set('section_id', FILESECTIONID);
			$entry->setData(FILEUPLOADID, array(
					'file' => $uploaddir . $data[$i][0],
					'mimetype' => $data[$i][1],
					'size' => $data[$i][4]
			));
			$entry->setData(SELECTBOXLINKID, array('relation_id' => $entry_id));
			$entry->commit();
		}
		return array();
	}

	public function createTable(){
		
		return $this->Database->query(
		
			"CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `entry_id` int(11) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `entry_id` (`entry_id`),
			) TYPE=MyISAM;"
		
		);
	}
}

