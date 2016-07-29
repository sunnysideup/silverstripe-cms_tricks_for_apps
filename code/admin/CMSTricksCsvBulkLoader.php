<?php

class CMSTricksCsvBulkLoader extends CsvBulkLoader {

	/**
	 * Map columns to DataObject-properties.
	 * If not specified, we assume the first row
	 * in the file contains the column headers.
	 * The order of your array should match the column order.
	 *
	 * The column count should match the count of array elements,
	 * fill with NULL values if you want to skip certain columns.
	 *
	 * You can also combine {@link $hasHeaderRow} = true and {@link $columnMap}
	 * and omit the NULL values in your map.
	 *
	 * Supports one-level chaining of has_one relations and properties with dot notation
	 * (e.g. Team.Title). The first part has to match a has_one relation name
	 * (not necessarily the classname of the used relation).
	 *
	 * <code>
	 * <?php
	 * 	// simple example
	 *  array(
	 *  	'Title',
	 * 		'Birthday'
	 * 	)
	 *
	 * // complex example
	 * 	array(
	 * 		'first name' => 'FirstName', // custom column name
	 * 		null, // ignored column
	 * 		'RegionID', // direct has_one/has_many ID setting
	 * 		'OrganisationTitle', // create has_one relation to existing record using $relationCallbacks
	 * 		'street' => 'Organisation.StreetName', // match an existing has_one or create one and write property.
	 * 	);
	 * ?>
	 * </code>
	 *
	 * @var array
	 */
	public $columnMap = array();

	/**
	 * Find a has_one relation based on a specific column value.
	 *
	 * <code>
	 * <?php
	 * array(
	 * 		'OrganisationTitle' => array(
	 * 			'relationname' => 'Organisation', // relation accessor name
	 * 			'callback' => 'getOrganisationByTitle',
	 *		);
	 * );
	 * ?>
	 * </code>
	 *
	 * @var array
	 */
	public $relationCallbacks = array();

	/**
	 * Specifies how to determine duplicates based on one or more provided fields
	 * in the imported data, matching to properties on the used {@link DataObject} class.
	 * Alternatively the array values can contain a callback method (see example for
	 * implementation details). The callback method should be defined on the source class.
	 *
	 * NOTE: If you're trying to get a unique Member record by a particular field that
	 * isn't Email, you need to ensure that Member is correctly set to the unique field
	 * you want, as it will merge any duplicates during {@link Member::onBeforeWrite()}.
	 *
	 * {@see Member::set_unique_identifier_field()}.
	 *
	 * If multiple checks are specified, the first one "wins".
	 *
	 *  <code>
	 * <?php
	 * array(
	 * 		'customernumber' => 'ID',
	 * 		'phonequestionnaire_fieldsnumber' => array(
	 * 			'callback' => 'getByImportedPhoneNumber'
	 * 		)
	 * );
	 * ?>
	 * </code>
	 *
	 * @var array
	 */
	public $duplicateChecks = array(
		'Code' => 'Code'
	);

	/**
	 * @var Boolean $clearBeforeImport Delete ALL records before importing.
	 */
	public $deleteExistingRecords = false;

	/**
	 * Each row in the imported dataset should map to one instance
	 * of this class (with optional property translation
	 * through {@self::$columnMaps}.
	 *
	 * @var string
	 */
	public $objectClass = "";


	/**
	 * Override this on subclasses to give the specific functions names.
	 *
	 * @var string
	 */
	public static $title = "Import Stuff";


	/**
	 * Delimiter character (Default: comma).
	 *
	 * @var string
	 */
	public $delimiter = ',';

	/**
	 * Enclosure character (Default: doublequote)
	 *
	 * @var string
	 */
	public $enclosure = '"';

	/**
	 * Identifies if the has a header row.
	 * @var boolean
	 */
	public $hasHeaderRow = true;


	public function getExportExampleData(){
		return <<<CSV
Code,Title,Archived
"CODE-FOR-IMPORT","Title for import",0
CSV;
	}


}
