<?php

namespace Converter;


class Converter{

    protected static  $_LOG='log.txt';

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

                    self::log('Wrong type for > '.$infoFile->getFilename());
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
    //fonction pour nettoyer les noms des fichiers
    //il ne faut pas d'espace et pas de ' en début ou en fin

    public static function clean($array,string $src,string $dest):bool {

        
        foreach($array as $tune){


            $basename=str_replace('\'',"~",$tune);
            $basename=str_replace(" ","_",$basename);
            
            rename($src.'/'.$tune,$dest.'/'.$basename);

        }
        return true;
        
    }

    public static function readable($array,string $src,string $dest):bool {

        
        foreach($array as $tune){


            $basename=str_replace('~','\'',$tune);
            $basename=str_replace("_", " ",$basename);
            
            rename($src.'/'.$tune,$dest.'/'.$basename);

        }
        return true;
        
    }

    public static function log(string $errorMessage){

        $handle=fopen(self::$_LOG,'a');
        fwrite($handle,$errorMessage.PHP_EOL);
        fclose($handle);

    }
}