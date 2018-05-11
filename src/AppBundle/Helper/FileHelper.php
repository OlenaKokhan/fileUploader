<?php

namespace AppBundle\Helper;


use AppBundle\Storage\RedisCacheRepository;

class FileHelper
{
    /**
     * @var
     */
    private $redis;
    /**
     * @var
     */
    private $targetDirectory;

    /**
     * FileUploader constructor.
     * @param $targetDirectory
     * @param RedisCacheRepository $redis
     */
    public function __construct($targetDirectory, RedisCacheRepository $redis)
    {
        $this->targetDirectory = $targetDirectory;
        $this->redis = $redis;
    }

    function split_file($fileName, $parts_num)
    {

        $handle = fopen($this->targetDirectory . '/' . $fileName, 'rb') or die("error opening file");

        $file_size = filesize($this->targetDirectory . '/' . $fileName);
        $parts_size = floor($file_size / $parts_num);
        $modulus = $file_size % $parts_num;
        for ($i = 0; $i < $parts_num; $i++) {
            if ($modulus != 0 & $i == $parts_num - 1){
                $parts[$i] = fread($handle, $parts_size + $modulus) or die("error reading file");
            }else{
                $parts[$i] = fread($handle, $parts_size) or die("error reading file");
            }

            $this->redis->set($i . '_' . $fileName, $parts[$i]);

        }
        //close file handle
        fclose($handle) or die("error closing file handle");
        unlink($this->targetDirectory . '/' . $fileName);

        return 'OK';
    }


    function merge_file($fileName, $parts_num)
    {
        $content = '';
        //put splited files content into content
        for ($i = 0; $i < $parts_num; $i++) {
            $content .= $this->redis->getByHash($i . '_' . $fileName);
        }
        //write content to merged file
        $handle = fopen($this->targetDirectory . '/' . $fileName, 'wb') or die("error creating/opening merged file");
        fwrite($handle, $content) or die("error writing to merged file");
        return 'OK';
    }

}
