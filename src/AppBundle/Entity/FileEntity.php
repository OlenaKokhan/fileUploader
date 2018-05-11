<?php


namespace AppBundle\Entity;


class FileEntity
{
    private $file;

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}