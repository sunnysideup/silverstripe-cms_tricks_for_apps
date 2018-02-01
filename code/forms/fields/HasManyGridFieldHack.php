<?php

/**
 * this->owner mother of all ugly hacks ensures
 * that if you open a Has Many Entry from an Has One Parent Data Object
 * in modeladmin (e.g. adding a city to a country) that the
 * city field is pre-completed.
 *
 *
 *
 *
 */

class HasManyGridFieldHack_HasManyList extends Extension
{
    public function getForeignKey()
    {
        return $this->owner->foreignKey;
    }
}

class HasManyGridFieldHack extends Extension
{
    public function updateItemEditForm($form)
    {
        $list = $this->owner->gridField->getList();
        if ($list && $list instanceof HasManyList) {
            $foreignKey = $list->getForeignKey();
            $dataClass = $list->dataClass();
            $dataQuery = $list->dataQuery();
            $foreignID = $list->getForeignID();
            $fields = $form->Fields();
            $field = $fields->dataFieldByName($foreignKey);
            if ($field) {
                $field->setValue($foreignID);
                $fields->replaceField($field->Name, $field->performDisabledTransformation());
            }
            $this->owner->record->$foreignKey = $foreignID;
        }
    }
}
