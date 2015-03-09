<?php

class Zend_View_Helper_BuildParams extends Zend_View_Helper_Placeholder_Container_Standalone
{
    public function buildParams($page, $sort, $filter)
    {
        $data = array(
            'page' => $page,
            'sort[sort_field]' => $sort['sortField'],
            'sort[sort_type]' => $sort['sortType']
        );
        if (isset($filter['search'])) {
            foreach ($filter['search'] as $key => $val) {
                foreach ($val as $k => $v) {
                    $data['search[' . $key . '][' . $k . ']'] = $v;
                }
            }
        }
        return $data;
    }
}
