<?php



class digXML {

    // mes constantes pour le nettoyage de mon dossier tuenXML lors de l'export 
    private const FILETODELETE=['layout-cache','manifest.xml','manifest.rdf','meta.xml','mimetype','settings.xml','styles.xml',
'accelerator','floater','images','menubar'];
    private const FOLDERTODELETE=['Thumbnails','Pictures','META-INF','Configurations2'];


    private static function openFolder(string $pathname):array // renvoie un tableau qui contient l'ensemble des noms de fichiers dans mon dossier src
    {

    $nameFiles=[];

    if(is_dir($pathname)){

            if($nameFiles=scandir($pathname)){

                unset($nameFiles[0]);//afin de supprimer '.'
                unset($nameFiles[1]);//afin de suprrimer '..'
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
            // retourner le tableau
        }
        else{

            die('Une erreur est survenue lors de la création du dossier.');

        }
        

    }   

}

    private static function cleanXmlFolder():bool 
    {

        // clean the folder deleting files from the list FILETODELETE above
        foreach(self::FILETODELETE as $file){
            if(is_file(__DIR__.'/tuneXml/'.$file)){

                unlink(__DIR__.'/tuneXml/'.$file);
            }
        }
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

            //cleaning the destination folder
            foreach(self::FOLDERTODELETE as $target){

                self::recurRm('tuneXml/'.$target);
            }
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

    //mreturn an array title=>, author => 

    public static function getInfoTune($fileName) :array
    {
    
        $info=[];
        $_text=[];
        $flag=0;

        if($content=self::readXML($fileName)){

            foreach($content as $item){

    
                if($item["name"]=='#text' && $item['value'] !=' ' ){
                    array_push($_text,$item['value']);
                    $flag ++;
    
                    if($flag==2){
    
                        $info['auteur']=$row["value"]; // attention l'auteur est parfois plus loin
                        break;
                    }                
                }
               
            
        }
        
        
    
            $position = strpos($fileName,'.');
            $info['titre']=substr($fileName, 0, $position);
            $info['auteur']=$_text[1];
        }
        
        return $info;

    }    

// insert all the tune in DB

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


    //return the content of a fodler with no file in it

    function contentFolder(string $filename) 
{

    $content = scandir($filename);
    $orderedContent=[];

    foreach($content as $item){

       
            if(!is_file($filename.'/'.$item))
            {

                array_push($orderedContent,$item);
               
            }
        
            else{
                echo 'suppresion de '.$filename.'/'.$item;
                unlink($filename.'/'.$item);
            }

        
    }

    unset($orderedContent[0]);
    unset($orderedContent[1]);

    return $orderedContent;

}


function folderIsEmpty($content):bool 
{

    if(empty($content)){

        echo 'folder is empty.';
        return true;
    }
    else{

        echo "folder isn't empty.<br>";
        return false;
    }

}



function removeFolder($filename){

    if(rmdir($filename)){

       
        echo('suppression de '.$filename);
    }
    else{

        echo 'impossible de rm.';
    }
}

// function removeFile($filename){

//     if(is_file($filename)){

//         unset($filename);
//     }
// }

private static function recurRm($filename){

    echo $filename.'<br>';

    var_dump(contentFolder($filename));

   if(folderIsEmpty(contentFolder($filename))){
        removeFolder($filename);
   }
   else{

        foreach(contentFolder($filename) as $item){

            recurRm($filename.'/'.$item);

        }
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

