<?php


class GridFieldExtraColumns implements GridField_ColumnProvider {

	protected $field = '';

	public function __construct($field) {
		$this->field = $field;
	}

	public function augmentColumns($gridField, &$columns) {
		array_unshift($columns, $this->field);
	}

	public function getColumnAttributes($gridField, $record, $columnName) {
		return array(
			'data-extrafield' => '1',
			'data-url' => '/admin/boardcheck',
			'data-field' => $this->field,
			'data-id' => $record->ID,
			'onclick' => "if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;var val = prompt('Enter value');var t = jQuery(this);t.html(val);var model = t.parents('tr').data('class');jQuery.post(t.data('url') + '/' + model + '/extrafields/' + t.data('id'),{field:t.data('field'),table:t.data('table'),id:t.data('id'),value:val});return false"
			);
	}

	public function getColumnContent($gridField, $record, $columnName) {
		$res = $gridField->getList()->getExtraData($this->field,$record->ID);
		if($res) {
			return $res[$this->field];
		}
	}

	public function getColumnMetadata($gridField, $columnName) {
		return array('title' => $this->field);
	}

	public function getColumnsHandled($gridField) {
		$form = $gridField->getConfig()->getComponentByType('GridFieldDetailForm');
		if($form) {
			$field = $this->field;
			//this does nothing :(
			$form->setItemEditFormCallback(function($form,$components) use ($field) {
				$form->addFieldToTab('Root.Main',new TextField($field));
			});
		}
		return array($this->field);
	}
}
