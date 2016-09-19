<?php

class AutoCompleteDropdownField extends DropdownField
{
    public function __construct($name, $title=null, $source=array(), $value='', $form=null, $emptyString=null)
    {
        parent::__construct($name, $title, $source, $value, $form, $emptyString);
        $this->addExtraClass('dropdown');
        Requirements::javascript(FRAMEWORK_DIR . '/thirdparty/jquery-entwine/dist/jquery.entwine-dist.js');
        Requirements::javascript('cms_tricks_for_apps/javascript/jquery-ui-1.10.4.custom.min.js');
        Requirements::javascript('cms_tricks_for_apps/javascript/autocompletedropdownfield.js');
    }
}
