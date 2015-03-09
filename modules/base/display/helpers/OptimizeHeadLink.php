<?php

class Zend_View_Helper_OptimizeHeadLink extends Zend_View_Helper_HeadLink
{
    public function optimizeHeadLink()
    {
        $registry = Zend_Registry::getInstance();
        if(!$registry->isRegistered('headLink')) {
            return $this;
        }
        foreach ($registry->headLink ? $registry->headLink : array() as $index => $value) {
            switch (true) {

                case 0 === strpos($index, 'file'):
                    $this->appendStylesheet(Base_Controller_Helper_Path2Url::getInstance()->direct($value));
                    break;
            }
        }
        return $this;
    }
}
