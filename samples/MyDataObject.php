<?php

class MyDataObject extends DataObject {

	private static $db = array(
		'Code' => 'Int',
		'Title' => 'Varchar(255)',
		'NumericalValue' => 'Int'
		'Required' => 'Boolean'
	);

	private static $has_one = array(
		"MyHasOne" => "MyHasOne",
		"RequiredNice" => "MyHasOne"
	);

	private static $has_many = array(
	);

	private static $many_many = array(
		'MyParentDataObjects' => 'MyParentDataObject'
	);

	private static $belongs_many_many = array(
	);

	private static $casting = array(
		"RequiredNice" => "Varchar"
	);

	private static $indexes = array(
		"FullTitle" => "Varchar"
	);

	private static $default_sort = 'MyDataObject.NumericalValue ASC';

	private static $required_fields = array();

	private static $summary_fields = array(
		'FullTitle' => 'Title (Code: Title)',
		'RequiredNice' => 'Is this Required'
	);

	private static $field_labels = array(
	);

	private static $searchable_fields = array(
	);

	private static $singular_name = "My Data Object";

	private static $plural_name = "My Data Objects";

	function CMSEditLink() {
		$controller = singleton("SampleModelAdmin");
		return $controller->Link()."MyDataObject/EditForm/field/MyDataObject/item/".$this->ID."/edit";
	}

	function getCMSFields() {
		$fieldLabels = $this->config()->get('field_labels');
		if($this->exists()) {
			$field = $fields->findOrMakeTab('Root.MyParentDataObjects')->fieldByName('MyParentDataObjects');
			$field->getConfig()
				->removeComponentsByType('GridFieldAddExistingAutocompleter')
				->removeComponentsByType('GridFieldAddNewButton');
		}
		CMSTricks::add_links_to_fields($this, $fields);
		return $fields;
	}

	/**
	 * returns list of fields as they are exported
	 * @return array
	 * Field => Label
	 */
	function getExportFields(){
		return array(
			'MyHasOneID' => 'MyHasOne  ID',
			'MyHasOne.Title' => 'MyHasOne Title',
			'ID' => 'ID',
			'Title' => 'Title'
		);
	}

	function FullTitle() {return $this->getFullTitle();}
	function getFullTitle() {
		return $this->Code.": ".$this->Title;
	}


	function canCreate($member = null) {
		return true;
	}

	function canView($member = null) {
		return true;
	}

	function canEdit($member = null) {
		return ! $this->MyParentDataObjects()->count();
	}


	function canDelete($member = null) {
		return false;
	}

	//function RequiredNice(){return $this->getRequiredNice();}
	function getRequiredNice(){
		return $this->dbObject('Required')->Nice();
	}


	public function requireDefaultRecords(){
		parent::requireDefaultRecords();
	}

	public function populateDefaults() {
		parent::populateDefaults();
		if(!$this->NumericalValue) {
			$this->NumericalValue = 999999;
		}
	}

	function onBeforeWrite(){
		parent::onBeforeWrite();
	}

	function onAfterWrite(){
		parent::onAfterWrite();
	}


}
