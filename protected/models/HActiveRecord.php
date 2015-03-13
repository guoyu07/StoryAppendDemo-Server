<?php

/**
 * Class HActiveRecord
 * override getAttributes method to merge virtual properties;
 */
abstract class HActiveRecord extends CActiveRecord
{
    public function getAttributes($named = true)
    {
        $attr = parent::getAttributes($named);
        $attr = array_merge($attr, get_object_vars($this));
        return $attr;
    }
}
