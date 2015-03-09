<?php


class Base_Constant_Server extends Base_Php_Overloader
{

    CONST FIRST_TEMP_DIRECTORY = 'temp';
    CONST LAST_TEMP_DIRECTORY = LAST_PROJECT_TEMP;
    CONST TARGET = 'target';
    CONST TIME_REMEMBER_LOGIN = 604800;

    const MEDIA_TEMP = 'temp';
    const MEDIA_UPLOAD = 'upload';
    const MEDIA = PROJECT_MEDIA;
    const ORIGINAL_PICTURE_DIRECTORY = 'p0x'; // origin directory
    const SMALL_PICTURE_DIRECTORY = 'p123x';
    const MEDIUM_PICTURE_DIRECTORY = 'p303x';
    const LARGE_PICTURE_DIRECTORY = 'p490x';
    const SMALL_PICTURE_SIZE = 32; // size in px
    const MEDIUM_PICTURE_SIZE = 303;
    const LARGE_PICTURE_SIZE = 490;
    const MIN_IMAGE_SIZE = 1000;
    // php.ini
    // memory_limit = 512M
    // upload_max_file_size = 3M
    // post_max_size = 100M
    // file_uploads = On
    const MAX_IMAGE_SIZE = 3145728;
    static $allows = array('jpg', 'jpeg', 'png', 'tiff', 'tif', 'gif');
    static $allowFiles = array('jpg', 'jpeg', 'png', 'tiff', 'tif', 'gif', 'doc', 'docx', 'pdf');

    private static $_instance = NULL;
    /**
     * Validate Regex Pattern array
     */
    static $validateRegex = array(
        'email' => '/^\'?(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))\'?$/iD',
        'tel' =>'/^\+?[0-9]{6,15}$/',
        'alphabet' => '/^[a-zA-Z]{1,}/',
        'number' => '/^[0-9]{1,}$/',
        'name' => '/^[a-zA-Z\"\-\'\ ]{1,}$/',
        'company_name' => '/^[a-zA-Z0-9\"\-\'\ ]{1,}$/',
        'postcode' => '/^[0-9]{3,7}$/',
        'address' => '/^[a-zA-Z0-9\-\ \'\"\,\/]{2,}$/',
        'password' => '/^[a-zA-Z0-9\ \-]{8,}$/',
        'ccv' => '/^[0-9]{3}$/',
        'image' => '/([a-zA-Z0-9\.\_\-\+\%\@\$\#])*\.(png|jpe?g|gif|PNG|JPE?G|GIF)$/',
        'file' => '/([a-zA-Z0-9\.\_\-\+\%\@\$\#])*\.(png|jpe?g|gif|PNG|JPE?G|GIF|docx?|DOCX?|pdf|PDF|xlsx?|XLSX?)$/',
        'float' => '/^-?(?:\d+|\d*\.\d+)$/',
        'required' => '/^.{1,}$/',
    );

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function sureLastTemp($target = null)
    {
        $tempDir = WWW_PATH . DS . self::LAST_TEMP_DIRECTORY . DS . ltrim($target, '/\\');
        $tempDir = rtrim($tempDir, '/\\');
        is_dir($tempDir) || mkdir($tempDir, 0777, TRUE);
        return $tempDir;
    }

    public static function getFirstTemp()
    {
        $tempDir = WWW_PATH . DS . self::FIRST_TEMP_DIRECTORY;
        is_dir($tempDir) || mkdir($tempDir, 0777, TRUE);
        return $tempDir;
    }

    public static function getLastTemp()
    {
        return self::sureLastTemp();
    }
}