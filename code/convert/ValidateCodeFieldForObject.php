<?php
/**
 */

class ValidateCodeFieldForObject extends Object
{
    private static $length = 7;

    /**
     * @var Array
     */
    private $replacements = array(
        '/&amp;/u' => '-and-',
        '/&/u' => '-and-',
        '/\s/u' => '-', // remove whitespace
        '/[^A-Za-z0-9.\-_]+/u' => '', // remove non-ASCII chars, only allow alphanumeric, dashes and dots.
        '/[\-]{2,}/u' => '-', // remove duplicate dashes
        '/[\_]{2,}/u' => '_', // remove duplicate underscores
        '/^[\.\-_]/u' => '', // Remove all leading dots, dashes or underscores
    );

    /**
     * makes sure that code is unique and gets rid of special characters
     * should be run in onBeforeWrite
     *
     * @param DataObject | String $obj
     * @param Boolean $createCode
     */

    public function checkCode($obj, $createCode = false)
    {
        //exception dealing with Strings
        $isObject = true;
        if (! is_object($obj)) {
            $str = $obj;
            $obj = new DataObject();
            $obj->Code = strval($str);
            $isObject = false;
        }
        if ($createCode) {
            if (!$obj->Code || strlen($obj->Code) != $this->Config()->get("length")) {
                $obj->Code = substr(md5(uniqid($obj->ID, true)), 0, $this->Config()->get("length"));
            }
        }
        $obj->Code = trim($obj->Code);
        foreach ($this->replacements as $regex => $replace) {
            $obj->Code = preg_replace($regex, $replace, $obj->Code);
        }
        if (!$obj->Code) {
            "CODE-NOT-SET";
        }
        //make upper-case
        $obj->Code = trim(strtoupper($obj->Code));
        //check for other ones.
        $count = 2;
        $code = $obj->Code;
        while ($isObject && $obj::get()
            ->filter(array("Code" => $obj->Code))
            ->exclude(array("ID" => $obj->ID))->Count()
        ) {
            $obj->Code = $code . '-' . $count;
            $count++;
        }
        return $obj->Code;
    }
}
