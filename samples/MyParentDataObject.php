<?php

class MyParentDataObject extends DataObject {

	/**
	 */
	private static $db = array(
		'Code' => 'Int',
		'Title' => 'Varchar(255)'
	);

	private static $belongs_many_many = array(
		'MyDataObjects' => 'MyDataObject'
	);

}
