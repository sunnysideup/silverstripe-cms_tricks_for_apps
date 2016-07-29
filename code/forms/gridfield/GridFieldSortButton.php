<?php

/**
 * Adds an "SortList" button to the bottom or top of a GridField.
 *
 * @package framework
 * @subpackage fields-gridfield
 */
class GridFieldSortButton implements GridField_HTMLProvider, GridField_ActionProvider, GridField_URLHandler {

	/**
	 * @param string $targetFragment The HTML fragment to write the button into
	 * @param array $sortListColumns The columns to include in the sortList view
	 */
	public function __construct($targetFragment = "before") {
		$this->targetFragment = $targetFragment;
	}

	/**
	 * Place the sortList button in a <p> tag below the field
	 *
	 * @param GridField
	 *
	 * @return array
	 */
	public function getHTMLFragments($gridField) {
		$button = new GridField_FormAction(
			$gridField,
			'sortlist',
			_t('TableListField.SortList', 'Sort List'),
			'sortList',
			null
		);

		$button->setAttribute('data-icon', 'grid_sortList');
		$button->setAttribute('data-icon', 'grid_sortList');
		$button->addExtraClass('no-ajax');

		return array(
			$this->targetFragment => '<span class="grid-sortList-button">'.DataObjectSorterController::popup_link("MyClassNameGoesHere", "", "", "sort") .'</span>',
		);
	}

	/**
	 * SortList is an action button.
	 *
	 * @param GridField
	 *
	 * @return array
	 */
	public function getActions($gridField) {
		return array('sortlist');
	}

	/**
	 * Handle the sortList action.
	 *
	 * @param GridField
	 * @param string
	 * @param array
	 * @param array
	 */
	public function handleAction(GridField $gridField, $actionName, $arguments, $data) {

	}

	/**
	 * SortList is accessible via the url
	 *
	 * @param GridField
	 * @return array
	 */
	public function getURLHandlers($gridField) {
		return array(
			'sortList' => 'handleSortList',
		);
	}


}
