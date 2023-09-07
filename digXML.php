<?php



class digXML {

    private const FILETODELETE=['layout-cache','manifest.xml','manifest.rdf','meta.xml','mimetype','settings.xml','styles.xml',
'accelerator','floater','images','menubar'];


    private static function openFolder(string $pathname):array // renvoie un tableau qui contient l'ensemble des noms de fichiers dans mon dossier src
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

    private static function cleanXmlFolder():bool
    {

        // clean the file 
        foreach(self::FILETODELETE as $file){
            if(is_file(__DIR__.'/tuneXml/'.$file)){

                unlink(__DIR__.'/tuneXml/'.$file);


            }

        }

        //clean the folder 
        return true;

    }
    // fonction qui va extraire le xml d'un odt et l'ecrire dans un nouveau dossier

    public static function digXML(string $src, string $dest):bool
    {
            
        if(is_dir($dest)){


            $listeNom=self::openFolder($src);

            foreach($listeNom as $nom){

                $zip = new ZipArchive();
                $zip->open($src.'/'.$nom);
                $zip->extractTo($dest);
                $zip->close();
                $newName=basename($nom,'.odt').'.xml';
                rename($dest.'/content.xml', $dest.'/'.$newName);
                chmod('tuneXml/'.$newName,777);
            }

            self::cleanXmlFolder();
            return true;
         
        }
    }


    public static function readXML(string $fileName):array|bool
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
        $_text=[];
        $flag=0;

        if($content=self::readXML($fileName)){

            foreach($content as $item){

    
                if($item["name"]=='#text' && $item['value'] !=' ' ){
                    array_push($_text,$item['value']);
                }
               
            
        }
        
        
    
            $position = strpos($fileName,'.');
            $info['titre']=substr($fileName, 0, $position);
            $info['auteur']=$_text[1];
        }
        
        return $info;

    }    


    public static function injectionDb()
    {
        $pdo=ConnexionDb::getInstance();

        $listeGrillesXml =self::openFolder('tuneXml');

        $query='INSERT INTO tune (titre,auteur)VALUES(:titre,:auteur)';
        foreach($listeGrillesXml as $item){
            $statement = $pdo->prepare($query);
            $tune=self::getInfoTune($item) ;
            $statement->execute(array(':titre'=>$tune['titre'],':auteur'=>$tune['auteur']));
        }

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

