<?php

class MyDataObjectImport extends CMSTricksCsvBulkLoader
{
    public $columnMap = array(
        'Code',
        'Title' => '->getTitle',
        'NumericalValue',
        'MyCodesCodes' => '->getMyCodeCodes'
    );

    public $objectClass = "MyDataObject";

    public static $title = "Import MyDataObjects";

    public function getTitle($obj, $val, $record)
    {
        if (!$val && ($val === 0 || $val === "0")) {
            $val = "0 (Zero)";
        }
        if (!$val && ($val === null || $val === "")) {
            $val = "none";
        }
        $obj->Title = $val;
    }

    public function getMyCodeCodes($obj, $val, $record)
    {
        $count = 0;
        if ($val) {
            $obj->write();
            $idArray = $obj->MyParentDataObjects()->map("ID", "ID")->toArray();
            $filter = CodeFilter::create();
            $valArray = explode(",", $val);
            foreach ($valArray as $key => $myParentDataObjectCode) {
                $valArray[$key] = $filter->checkCode($myParentDataObjectCode);
                $newCode = $valArray[$key];
                $newMyParentDataObject = MyParentDataObject::get()
                    ->filter(array("Code" => $newCode))
                    ->First();
                if (!$newMyParentDataObject) {
                    $newMyParentDataObject = new MyParentDataObject();
                    $newMyParentDataObject->Title = $newCode;
                    $newMyParentDataObject->Code = $newCode;
                    $newMyParentDataObject->write();
                }
                $idArray[$newMyParentDataObject->ID] = $newMyParentDataObject->ID;
            }
            $myParentDataObjects = MyParentDataObject::get()
                ->filter(array("ID" => $idArray));
            if ($myParentDataObjects->count()) {
                foreach ($myParentDataObjects as $myParentDataObject) {
                    $obj->MyParentDataObjects()->add($myParentDataObject);
                    DB::query("
						UPDATE \"MyParentDataObject_MyDataObjects\"
						SET \"MyOtherField\" = ".$count."
						WHERE
							\"MyParentDataObjectID\" = ".$myParentDataObject->ID." AND
							\"MyDataObjectID\" = ".$obj->ID." "
                    );
                    $count++;
                }
            }
        }
    }

    public function getExportExampleData()
    {
        return <<<CSV
Code,Title,NumericalValue,MyCodesCodes
"CODE-FOR-MY-DATA-OBJECT-IMPORT","Title for import",11, "code1,code2, code-3"

CSV;
    }

    public function getExportExampleDataForOneList($myParentDataObject)
    {
        $example =  <<<CSV
Code,Title,NumericalValue,MyCodesCodes

CSV;
        if ($myParentDataObject->Codes()->count()) {
            foreach ($myParentDataObject->Codes() as $myDataObject) {
                $example .= <<<CSV
"{$myDataObject->Code}","{$myDataObject->Title}",{$myDataObject->NumericalValue},"{$myParentDataObject->Code}"

CSV;
            }
        } else {
            $example .= <<<CSV
"CODE-FOR-MY-DATA-OBJECT-IMPORT-1","Title 1 for MyDataObject 1 import",1,"{$myParentDataObject->Code}"
"CODE-FOR-MY-DATA-OBJECT-IMPORT-2","Title 2 for MyDataObject 2 import",12,"{$myParentDataObject->Code}"
"CODE-FOR-MY-DATA-OBJECT-IMPORT-3","Title 3 for MyDataObject 3 import",133,"{$myParentDataObject->Code}"
CSV;
        }
        return $example;
    }
}



class MyDataObjectImport_Replace extends Controller
{
    private static $allowed_actions = array(
        "index" => "ADMIN",
        "showonly" => "ADMIN",
        "deleteoptions" => "ADMIN",
        "downloadexample" => "ADMIN"
    );

    public function init()
    {
        parent::init();
        if (!Permission::check("ADMIN")) {
            return Security::permissionFailure($this);
        }
    }

    public function index()
    {
        $this->lists = MyParentDataObject::get();
        return $this->printList();
    }

    public function showonly($request)
    {
        $this->lists = MyParentDataObject::get()->filter("ID", $request->param("ID"));
        return $this->printList();
    }

    private function printList()
    {
        echo "
		<h1>replace list</h1>
		<p>How this works</p>
		<ol>
			<li>download an import example below</li>
			<li>remove the MyDataObjects for your selected MyParentDataObject</li>
			<li><a href=\"".$this->uploadLink()."\">import the new entries</a></li>
		</ol>
		<ul>";
        foreach ($this->lists as $list) {
            echo "
			<li><strong>".$list->getFullTitle()."</strong>
				<blockquote>1. <a href=\"".$this->Link("downloadexample/".$list->ID."/")."\">download import example</a></blockquote>
				<blockquote>2. <a href=\"".$this->Link("deleteoptions/".$list->ID."/")."\">remove MyDataObjects</a></blockquote>
			</li>";
        }
        echo "
		</ul>";
    }

    public function deleteoptions($request)
    {
        if ($myParentDataObjectID = $request->param("ID")) {
            DB::query("DELETE FROM \"MyParentDataObject_MyDataObject\" WHERE \"MyParentDataObjectID\" = ".$myParentDataObjectID);
            echo "options deleted, <a href=\"".$this->uploadLink()."\">now import the new options</a>";
        }
    }

    public function downloadexample($request)
    {
        $myParentDataObject = MyParentDataObject::get()->byID($request->param("ID"));
        $importerClassName = "MyDataObjectImport";
        $objectClassName = "DataObjectImport";
        if (class_exists($objectClassName)) {
            $obj = new $importerClassName($objectClassName);
            if ($obj instanceof CsvBulkLoader) {
                if ($fileData = $obj->getExportExampleDataForOneList($list)) {
                    $fileName = $myParentDataObject->Code.".csv";
                    return SS_HTTPRequest::send_file($fileData, $fileName, 'text/csv');
                } else {
                    user_error("Could not export import example", E_USER_ERROR);
                }
            } else {
                user_error("$obj->class is not an instance of CsvBulkLoader");
            }
        }
    }

    public function Link($action = null)
    {
        $action = $action ? $action : "";
        return "/replacelist/".$action;
    }

    protected function uploadLink()
    {
        return "/admin/MyDataObjectsURL/MyDataObject/";
    }
}
