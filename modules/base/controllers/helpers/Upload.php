<?php

class Base_Controller_Helper_Upload extends Zend_Controller_Action_Helper_Abstract
{
    public function initOther()
    {
        $registry = Zend_Registry::getInstance();
        $reflection = new ReflectionClass('Base_Constant_Server');
        $registry->headScript['inline_' . count($registry->headScript)] =
            'var Base_Constant_Server = ' . Zend_Json_Encoder::encode($reflection->getConstants()) . ';';
    }

    public function index()
    {
        $list = array();
        $path = WWW_PATH . DS . Base_Constant_Server::MEDIA_TEMP . DS . Base_Constant_Server::SMALL_PICTURE_DIRECTORY;
        if (!is_dir($path)) {
            $this->view->list = $list;
            $this->_response->setBody($this->view->render($this->_verifyScriptName('image/index.phtml')));
            return;
        }
        $dirIterator = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::CHILD_FIRST | RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iterator as $path) {
            $info = pathinfo($path->__toString());
            $info['extension'] = strtolower($info['extension']);
            if (in_array($info['extension'], Base_Constant_Server::$allows)) {
                $list[$info['basename']] = str_replace(WWW_PATH, BASE_URL, $path->__toString());
                $list[$info['basename']] = str_replace(DS, UDS, $list[$info['basename']]);
            }
        }
        $this->view->list = $list;
        $this->_response->setBody($this->view->render($this->_verifyScriptName('image/index.phtml')));
    }

    public function upload($fieldName, &$errors = array())
    {
        $errors = array();
        if (!count($_FILES)) {
            $errors[] = 'Please choose a picture';
            $name = '';
        } else {
            $name = $_FILES[$fieldName]['name'];
        }
        // check multimedia type
        $info = pathinfo($name);
        $info['extension'] = strtolower($info['extension']);
        $quoteName = '';
        switch (true) {
            // check type
            case !in_array($info['extension'], Base_Constant_Server::$allows):
                $errors[] = 'File ' . $quoteName . ' type "' . $info['extension'] . '" is not supported. Supported types are ' . implode(', ', Base_Constant_Server::$allows);
                break;
            // check error
            case $_FILES[$fieldName]['error'] > 0:
                $errors[] = $quoteName . ' Can not upload the picture ';
                break;
            // check file size
            case $_FILES[$fieldName]['size'] < Base_Constant_Server::MIN_IMAGE_SIZE || Base_Constant_Server::MAX_IMAGE_SIZE < $_FILES[$fieldName]['size']:
                $errors[] = $quoteName . ' The file size should be between 1kB and ' . (Base_Constant_Server::MAX_IMAGE_SIZE/1024) . 'kB ('.(Base_Constant_Server::MAX_IMAGE_SIZE/1024)/1024 .'MB)';
                break;
            default:
                $imageSize = getimagesize($_FILES[$fieldName]['tmp_name']);
                if ($imageSize[0] < Base_Constant_Server::SMALL_PICTURE_SIZE) {
                    $errors[] = $quoteName . ' Minimum of width of the picture is ' . Base_Constant_Server::LARGE_PICTURE_SIZE;
                }
                break;
        }
        if ($errors) {
            return false;
        }

        $copied = array();
        $info = pathinfo($name);
        $info['extension'] = strtolower($info['extension']);
        $quoteName = '"' . $name . '"';
        $newName = Base_Controller_Helper_StandardFilename::getInstance()->direct($name, date('Ymd'), uniqid('', true)) . '.' . $info['extension'];
        $target = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP . DS . $newName;

        if (false === copy($_FILES[$fieldName]['tmp_name'], $target)) {
            $errors[] = $quoteName . '  Can not upload the picture ';
        } else {
             /*list($w) = getimagesize($target);
            $pxs = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP . DS . Base_Constant_Server::SMALL_PICTURE_DIRECTORY . DS . $newName;
            $small = $this->createPX($info['extension'], $target, $w, $pxs, Base_Constant_Server::SMALL_PICTURE_SIZE);
            $pxm = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP . DS . Base_Constant_Server::MEDIUM_PICTURE_DIRECTORY . DS . $newName;
            $medium = $this->createPX($info['extension'], $target, $w, $pxm, Base_Constant_Server::MEDIUM_PICTURE_SIZE);
            $pxl = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP . DS . Base_Constant_Server::LARGE_PICTURE_DIRECTORY . DS . $newName;
            $large = $this->createPX($info['extension'], $target, $w, $pxl, Base_Constant_Server::LARGE_PICTURE_SIZE);
            if ($small || $medium || $large) {
                $copied[] = array(
                    'old' => $name,
                    'new' => $newName,
                    'o' => $this->_helper->path2Url($target),
                    's' => $this->_helper->path2Url($pxs),
                    'm' => $this->_helper->path2Url($pxm),
                    'l' => $this->_helper->path2Url($pxl),
                );
            } else {
                $errors[] = $quoteName . ' Có lỗi khi tạo phiên bản px.';
            }*/
        }
        if ($errors) {
            if ($copied) {
                $errors[] = 'Some uploaded pictures are ' . implode(', ', $copied);
            }
            return false;
        }
        return $newName;
    }

    public function uploadFile($fieldName, &$errors = array())
    {
        $errors = array();
        if (!count($_FILES)) {
            $errors[] = 'Please choose a file';
            $name = '';
        } else {
            $name = $_FILES[$fieldName]['name'];
        }
        // check multimedia type
        $info = pathinfo($name);
        $info['extension'] = strtolower($info['extension']);
        $quoteName = '"' . $name . '"';
        switch (true) {
            // check type
            case !in_array($info['extension'], Base_Constant_Server::$allowFiles):
                $errors[] = 'File ' . $quoteName . ' type "' . $info['extension'] . '" is not supported. Supported types are ' . implode(', ', Base_Constant_Server::$allowFiles);
                break;
            // check error
            case $_FILES[$fieldName]['error'] > 0:
                $errors[] = $quoteName . ' Can not upload the file ';
                break;
            // check file size
            case $_FILES[$fieldName]['size'] < Base_Constant_Server::MIN_IMAGE_SIZE || Base_Constant_Server::MAX_IMAGE_SIZE < $_FILES[$fieldName]['size']:
                $errors[] = $quoteName . ' The file size should be between 1kB and ' . (Base_Constant_Server::MAX_IMAGE_SIZE/1024) . 'kB ('.(Base_Constant_Server::MAX_IMAGE_SIZE/1024)/1024 .'MB)';
                break;
            default:
                $imageSize = getimagesize($_FILES[$fieldName]['tmp_name']);
                break;
        }
        if ($errors) {
            return false;
        }

        $copied = array();
        $info = pathinfo($name);
        $info['extension'] = strtolower($info['extension']);
        $quoteName = '"' . $name . '"';
        $newName = Base_Controller_Helper_StandardFilename::getInstance()->direct($name, date('Ymd'), uniqid('', true)) . '.' . $info['extension'];
        $target = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP . DS . $newName;

        if (false === copy($_FILES[$fieldName]['tmp_name'], $target)) {
            $errors[] = $quoteName . ' Can not upload the file ';
        }
        if ($errors) {
            if ($copied) {
                $errors[] = 'Some uploaded files are ' . implode(', ', $copied);
            }
            return false;
        }
        return $newName;
    }

    private function createPX($type, $path0, $px0, $path2, $px2)
    {
        list($w, $h) = getimagesize($path0);

        if ($w < $px2 && $h < $px2) {
            return false;
        }
        // max width and height is px2
        //if ($w > $h) {
        //    $w2 = $px2;
        //    $h2 = $h * $px2 / $w;
        //    $h2 = (int)$h2;
        //} else {
        //    $h2 = $px2;
        //    $w2 = $w * $px2 / $h;
        //    $w2 = (int)$w2;
        //}
        if ($w >= $px2) {
            $w2 = $px2;
            $h2 = $h * $px2 / $w;
            $h2 = (int)$h2;
        } else {
            return false;
        }

        switch ($type) {
            case 'jpg':
            case 'jpeg':
                $src = imagecreatefromjpeg($path0);
                break;
            case 'png':
                $src = imagecreatefrompng($path0);
                break;
        }
        if (false === $src) {
            return false;
        }
        $new = imagecreatetruecolor($w2, $h2);
        if (false === $new) {
            return false;
        }
        if (false === imagecopyresampled($new, $src, 0, 0, 0, 0, $w2, $h2, $w, $h)) {
            return false;
        }
        switch ($type) {
            case 'jpg':
            case 'jpeg':
                $rs = imagejpeg($new, $path2, 100);
                break;
            case 'png':
                // http://stackoverflow.com/questions/7878754/creating-png-files
                // The problem is because image jpeg quality can be up to 100, whereas image png maximum quality is 9. try this
                $rs = imagepng($new, $path2, 9);
                break;
        }
        if (false === $rs) {
            return false;
        }
        if (false === imagedestroy($new)) {
            return false;
        }
        return true;
    }

    public function moveImage($file, $oldFile = null)
    {
        /** Move image to upload path and delete old file if exit */
        $tempPath = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP;
        $uploadPath = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_UPLOAD;

        rename($tempPath . DS . $file, $uploadPath . DS . $file);

        $olfFile = $uploadPath . DS . $oldFile;
        if (is_file($olfFile)) {
            unlink($olfFile);
        }
    }
    public function deleteImage($file, $folder = 'temp')
    {
        /** Delete file if exit */
        $tempPath = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP;
        $uploadPath = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_UPLOAD;
        if($folder == 'upload') $olfFile = $uploadPath . DS . $file;
        if($folder == 'temp') $olfFile = $tempPath . DS . $file;

        if (is_file($olfFile)) {
            unlink($olfFile);
        }
    }
    public function updateImage($newFile, $oldFile)
    {
        /** Delete image / file in upload folder and move image / file from temp folder to upload folder */
        $tempPath = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_TEMP;
        $uploadPath = WWW_PATH . DS . Base_Constant_Server::MEDIA . DS . Base_Constant_Server::MEDIA_UPLOAD;

        $olfFile = $uploadPath . DS . $oldFile;
        if (is_file($olfFile)) {
            unlink($olfFile);
        }
        rename($tempPath . DS . $newFile, $uploadPath . DS . $newFile);
    }
}
