<?php

class Zend_View_Helper_OptimizeHeadScript extends Zend_View_Helper_HeadScript
{

    public function optimizeHeadScript()
    {
        $registry = Zend_Registry::getInstance();
        $content = '';
        if(!$registry->isRegistered('headScript')) {
            return $this;
        }
        foreach ($registry->headScript ? $registry->headScript : array() as $index => $value) {
            switch (true) {

                case 0 === strpos($index, 'file'):
                    $this->appendFile(Base_Controller_Helper_Path2Url::getInstance()->direct($value));
                    break;

                case 0 === strpos($index, 'inline'):
                    $this->appendScript(';' . PHP_EOL . ';' . $value);
                    break;
            }
        }
        return $this;
    }
}
