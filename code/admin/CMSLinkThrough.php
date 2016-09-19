<?php

/**
 * Creates links through for
 *
 */

class CMSTricks extends Object
{
    private static $excluded_has_one_classes = array("Image", "File");

    private static $completion_array = array();

    private static $tab_name_for_link = "Root.Links";

    private static $tab_name_for_calc = "Root.Main";

    private static $excluded_casted_variables = array("Title", "CSSClasses");

    public static function add_links_to_fields($obj, $fields)
    {
        if (!isset(self::$completion_array[$obj->ClassName])) {
            self::$completion_array[$obj->ClassName] = true;
            if ($obj->exists()) {
                $fieldLabels = array_merge($obj->FieldLabels(), (array)$obj->Config()->get("field_labels"));
                $hasOneLinks = $obj->Config()->get("has_one");
                if (is_array($hasOneLinks) && count($hasOneLinks)) {
                    $linkTabName = Config::inst()->get("CMSTricks", "tab_name_for_link");
                    $fields->addFieldToTab($linkTabName, new LiteralField("YouAreLookingAt", '<h2>This objects links to ...</h2>'));
                    foreach ($hasOneLinks as $methodName => $className) {
                        $dbFieldName = $methodName."ID";
                        $label = isset($fieldLabels[$methodName]) ? $fieldLabels[$methodName] : null;
                        if ($obj->$dbFieldName && $relationObject = $obj->$methodName()) {
                            if (!in_array($relationObject->ClassName, Config::inst()->get("CMSTricks", "excluded_has_one_classes"))) {
                                $fields->addFieldToTab(
                                    $linkTabName,
                                    new LiteralField(
                                        "LinkThroughFor".$relationObject->ClassName.$relationObject->ID,
                                        '<h3><a href="'.$relationObject->CMSEditLink().'" target="_blank">open related '.$label.'</a></h3>'
                                    )
                                );
                            }
                        }
                    }
                }
                $castedVariables = $obj->Config()->get("casting");
                if (is_array($castedVariables) && count($castedVariables)) {
                    $calcTabName = Config::inst()->get("CMSTricks", "tab_name_for_calc");
                    $fields->addFieldToTab($calcTabName, new LiteralField("CalculatedValues", '<h2>Calculated Values ...</h2>'));
                    foreach ($castedVariables as $castedVariableName => $castedVariableType) {
                        if (!in_array($castedVariableName, Config::inst()->get("CMSTricks", "excluded_casted_variables"))) {
                            $label = isset($fieldLabels[$castedVariableName]) ? $fieldLabels[$castedVariableName] : null;
                            $fields->addFieldToTab(
                                $calcTabName,
                                new ReadonlyField(
                                    $castedVariableName,
                                    $label
                                )
                            );
                        }
                    }
                }
            }
        }
    }
}
