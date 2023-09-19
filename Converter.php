<?php

namespace Converter;


class Converter{


    public static function convert(string $srcFile, string $destFolder){

        //check destFolder exist
        if(is_dir($destFolder)){

            if(is_file($srcFile)){

                $infoFile=new \SplFileInfo(basename($srcFile));
                $ext=$infoFile->getExtension();
                

                if($ext==='odt'){

                    try{

                        $command = 'libreoffice --headless --convert-to pdf --outdir '.$destFolder.' '.$srcFile;
                        echo 'processing '.$srcFile.'<br>';
                        echo shell_exec($command).'<br>';

                    }
                    catch(ValueError $error){  

                        die($error->getMessage());
                    }
                    

                    
                }
                else{

                 die('Wrong type of file src => '.$ext.'. Odt format only supported.');
                }

            }
            else{
                die('Src File doesn\'t exists.');
            }
        }
        else{
            die('Destination folder doesn\'t exists.');
        }

    }
    
    public static function clean($array):array|bool {

        $res=[];

        foreach($array as $tune){

            $basename=str_replace(" ","_",$tune);
            $basename=str_replace('\'',"",$basename);
            rename('tuneOdt/'.$tune,'tuneOdt/'.$basename);

        }
        return true;
        
    }
}