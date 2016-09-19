<?php

class MyHasOne extends DataObject
{

    /**
     *
     * order specified by client.
     */
    private static $db = array(
        'Code' => 'Int',
        'Title' => 'Varchar(255)'
    );
}
