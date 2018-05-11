<?php

namespace AppBundle\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 */
class FileUploader
{
    /**
     * @var
     */
    private $targetDirectory;

    /**
     * FileUploader constructor.
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->getClientOriginalExtension();
        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    /**
     * @return mixed
     */
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}