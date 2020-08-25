<?php

require './vendor/autoload.php';

use thiagoalessio\TesseractOCR\TesseractOCR;
use Org_Heigl\Ghostscript\Ghostscript;

if(isset($_FILES['file'])){
    $file_name = $_FILES['file']['name'];
    $file_tmp =$_FILES['file']['tmp_name'];
    $file_name_without_ext = pathinfo($file_name, PATHINFO_FILENAME);

    move_uploaded_file($file_tmp,"files/".$file_name);

    $path = dirname(__FILE__) . '/files/';
    $pathToPdf = $path.$file_name;
    
    $gs = new Ghostscript ();
    Ghostscript::setGsPath('C:\Program Files\gs\gs9.52\bin\gswin64c.exe');

    // Set the output-device
    $gs->setDevice('jpeg')
        // Set the input file
        ->setInputFile($pathToPdf)
        // Set the output file that will be created in the same directory as the input
        ->setOutputFile($file_name_without_ext . '-%d')
        // Set the resolution to 96 pixel per inch
        ->setResolution(96)
        // Set Text-antialiasing to the highest level
        ->setTextAntiAliasing(Ghostscript::ANTIALIASING_HIGH)
        // Set the jpeg-quality to 100 (This is device-dependent!)
        ->getDevice()->setQuality(100);

    // convert the input file to an image   
    if (true === $gs->render()) {
        for($x = 1; $x <= 10; $x++) {
            if(file_exists('files/'.$file_name_without_ext .'-'.$x.'.jpeg')) {
                echo '<pre>';
                echo (new TesseractOCR('files/'.$file_name_without_ext .'-'.$x.'.jpeg'))->run();
            }
        }
    } else {
        echo 'some error occured';
    }
}
