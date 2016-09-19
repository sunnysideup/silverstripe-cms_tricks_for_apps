<?php


class ImportExamples extends ContentController
{
    private static $allowed_actions = array(
        "download" => "Admin"
    );

    public function download($request)
    {
        $importerClassName = $request->param("ID");
        $objectClassName = str_replace("Import", "", $importerClassName);
        if (class_exists($objectClassName)) {
            $obj = new $importerClassName($objectClassName);
            if ($obj instanceof CsvBulkLoader) {
                if ($fileData = $obj->getExportExampleData()) {
                    $fileName = $objectClassName.".csv";
                    return SS_HTTPRequest::send_file($fileData, $fileName, 'text/csv');
                } else {
                    user_error("Could not export import example", E_USER_ERROR);
                }
            } else {
                user_error("$obj->class is not an instance of CsvBulkLoader");
            }
        }
    }

    public static function get_link($className)
    {
        return "ImportExamples/download/{$className}Import/";
    }
}
