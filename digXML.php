<?php

class digXML{




   private function openFolder(string $pathname):array // renvoie un tableau qui contient l'ensemble des noms de fichiers dans mon dossier src
    {

    $nameFiles=[];

    if(is_dir($pathname)){

        echo('Le dossier est présent.<br>');

            if($nameFiles=scandir($pathname)){

                unset($nameFiles[0]);
                unset($nameFiles[1]);
                return $nameFiles;

            }
         
            else{
                
                die('impossible d\'ouvrir le dossier '.$pathname);
            }


        }

    
    else{

        echo('Création du dossier '.$pathname.'<br>');

        if(mkdir($pathname,777)){

            echo('Dossier créé.');
        }
        else{

            die('Une erreur est survenue lors de la création du dossier.');

        }
        

    }   

}

    private function digXML(string $src, string $dest)
    {
            
        if(is_dir($dest)){


            $listeNom=openFolder($src);

            foreach($listeNom as $nom){

                echo $nom.'<br>';
                $zip = new ZipArchive();
                $zip->open($src.'/'.$nom);
                var_dump($zip);
                $zip->extractTo($dest);
                $zip->close();
                $newName=basename($nom,'.odt').'.xml';
                rename($dest.'/content.xml', $dest.'/'.$newName);
                chmod('tuneXml/'.$newName,777);
            }
            

            
        }
        
        

        
    }


    private function readXML(string $fileName){

        if(file_exists("tuneXml/".$fileName)){

            $content=[];
            $row=[];
            $handle=new XMLReader();
            $handle->open('tuneXml/'.$fileName);
            while($handle->read()){
                    $row['name']=$handle->name;
                    $row['value']=$handle->value;

                    array_push($content,$row);
                }

            return $content;
        }

        else{

            die('le fichier n\exitse pas.');
        }
        
    }

public function getInfoTune($fileName) :array
{
   
    $info=[];
    $content=$this->readXML($fileName);
    $flag=0;
    foreach($content as $row){

        
        if($row['name']=='#text' && $flag==0){

            $position = strpos($fileName,'.');
      
     
      
            $info['titre']=substr($fileName, 0, $position);
            $flag ++;
        }        
        else if($row['name']=='#text' && $flag==1){
            $info['auteur']=$row["value"];
            $flag ++;
            break;
        }
            
        
     }
 
     return $info;
}

}
