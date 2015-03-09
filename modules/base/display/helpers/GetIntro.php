<?php

class Zend_View_Helper_GetIntro extends Zend_View_Helper_Placeholder_Container_Standalone
{
    public function getIntro($key = 'abn',$page_type = 'edit_profile', $auth_type = 'courier')
    {
        $messages = Base_Helper_Message::$listMessage;
        $introductions = $messages[$page_type][$auth_type];
        return $introductions[$key]['instruction'];
    }
}
