<?php

namespace App\Tools;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{

    public function save(UploadedFile $file, string $name, string $directory) : string {

        $newFileName = $name."-".uniqid().".".$file->guessExtension();
        $file->move($directory, $newFileName);
        return $newFileName;

    }



}