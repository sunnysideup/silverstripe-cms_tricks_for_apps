<?php

class SampleModelAdmin extends ModelAdmin
{
    private static $url_segment = 'myadmin';

    private static $menu_title = 'My Admin';

    private static $managed_models = array('MyDataObject');

    private static $model_importers = array(
        'MyDataObject' => 'MyDataObjectImport'
    );

    private static $menu_priority = 500;

    private static $menu_icon = "mysite/images/treeicons/myadmin.png";

    public function getExportFields()
    {
        $model = singleton($this->modelClass);
        if (method_exists($model, "getExportFields")) {
            $array = $model->getExportFields();
        } else {
            $array["ID"] = "ID";
            $array += $model->summaryFields();
        }
        return $array;
    }

    public function DownloadImportExampleLink()
    {
        return ImportExamples::get_link($this->modelClass);
    }


    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm();
        //This check is simply to ensure you are on the managed model you want adjust accordingly
        if ($gridField = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass))) {
            $gridField->getConfig()
                ->addComponent(new GridFieldSortableRows('Sort'));
            if ($this->modelClass == "Question") {
                $gridField->getConfig()
                    ->removeComponentsByType('GridFieldAddNewButton')
                    ->addComponent(new GridFieldAddNewMultiClass());
            }
        }
        return $form;
    }
}
