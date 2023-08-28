<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{   
    private $targetDirectory;
    
    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        try {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->targetDirectory, $fileName);
            
        } catch(FileException $e) {
            throw new FileException($e->getMessage());
        }
        
        return $fileName;
    }

    public function remove(string $image) : void {

        $previousImagePath = $this->targetDirectory . '/' . $image;
      
        try {

            if(file_exists($previousImagePath)) {
                unlink($previousImagePath);
            }

        } catch(FileException $e) {
            throw new FileException($e->getMessage());
        }
      
    }
}