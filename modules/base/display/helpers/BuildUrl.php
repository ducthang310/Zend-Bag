<?php

class Zend_View_Helper_BuildUrl extends Zend_View_Helper_Placeholder_Container_Standalone
{
    public function buildUrl($params, $updatedParams = array())
    {
        $params = array_merge($params, $updatedParams);
        $getParams = array();
        foreach ($params as $key => $value) {
            $getParams[] = rawurlencode($key) . '=' . rawurlencode($value);
        }
        return BASE_URL . UDS . Zend_Registry::getInstance()->moduleName . UDS . Zend_Registry::getInstance()->controllerName . '?' . ($getParams ? implode('&', $getParams) : '');
    }
}
