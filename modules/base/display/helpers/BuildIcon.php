<?php

class Zend_View_Helper_BuildIcon extends Zend_View_Helper_Placeholder_Container_Standalone
{
    public function buildIcon($sort, $type_sort_current, $name = '')
    {
        $icon = 'sort_down.png';
        if ($sort == $name && $type_sort_current == 'ASC') {
            $icon = 'sort_up.png';
        } else {
            $icon = 'sort_down.png';
        }
        return $icon;
    }
}
