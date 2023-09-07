<?php



class digXML {



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

    // fonction qui va extraire le xml d'un odt et l'ecrire dans un nouveau dossier

    private function digXML(string $src, string $dest):bool
    {
            
        if(is_dir($dest)){


            $listeNom=openFolder($src);

            foreach($listeNom as $nom){

                $zip = new ZipArchive();
                $zip->open($src.'/'.$nom);
                $zip->extractTo($dest);
                $zip->close();
                $newName=basename($nom,'.odt').'.xml';
                rename($dest.'/content.xml', $dest.'/'.$newName);
                chmod('tuneXml/'.$newName,777);
            }
         
        }
    }


    private static function readXML(string $fileName):array|bool
    {
        
        if(file_exists("tuneXml/".$fileName)){

            $content=[];
            $row=[];
            $handle=new XMLReader();

            if($handle->open('tuneXml/'.$fileName)){

                while($handle->read()){
                    $row['name']=$handle->name;
                    $row['value']=$handle->value;
                    array_push($content,$row);
                }
            }
            
            return $content;
        }

        else{

            return FALSE;
        }
        
    }

    //méthode appelé dans l'index 

    public static function getInfoTune($fileName) :array
    {
    
        $info=[];
        
        $flag=0;

        if($content=self::readXML($fileName)){

            foreach($content as $row){

                if($row['name']=='#text'){
    
                    $flag ++;
    
                    if($flag==2){
    
                        $info['auteur']=$row["value"]; // attention l'auteur est parfois plus loin
                        break;
                    }                
                }
            }
    
            $position = strpos($fileName,'.');
            $info['titre']=substr($fileName, 0, $position);
        }
        
        return $info;

    }    


    
}



// le singleton pour la connection à la base de données

class ConnexionDb extends PDO{

    //les constantes de connexion


    private const DBHOST = 'localhost';
    private const DBNAME ='inukshuk';
    private const DBUSER='nanook';
    private const DBPASS='ours';
    private static $instance=null;


    private function __construct(){

        $dns='mysql:dbhost='.self::DBHOST.';dbname='.self::DBNAME;
        parent::__construct($dns,self::DBUSER,self::DBPASS);

    }

    public static function getInstance(){

        if(self::$instance==null){
            return new self();
        }

        else{

            return self::instance;
        }
    }

}

