<?php



class digXML {

    // mes constantes pour le nettoyage de mon dossier tuenXML lors de l'export 
    private const FILETODELETE=['layout-cache','manifest.xml','manifest.rdf','meta.xml','mimetype','settings.xml','styles.xml',
'accelerator','floater','images','menubar'];
    private const FOLDERTODELETE=['Thumbnails','Pictures','META-INF','Configurations2'];


    public static function openFolder(string $pathname):array // renvoie un tableau qui contient l'ensemble des noms de fichiers dans mon dossier src
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
    
                        $info['auteur']=$item["value"]; // attention l'auteur est parfois plus loin
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

        $query='INSERT INTO tune (titre,auteurId,chemin)VALUES(:titre,(SELECT id FROM auteur WHERE nomAuteur= :auteur),:chemin)';

        foreach($listeGrillesXml as $item){
            if(!is_dir('tuneXml/'.$item)){

                $statement = $pdo->prepare($query);
                $tune=self::getInfoTune($item) ;
                $statement->execute(array(':titre'=>ucwords($tune['titre']),':auteur'=>$tune['auteur'],':chemin'=>'tunePdf/'.ucwords($tune['titre']).'.pdf'));
                 
            }
            
        }

    }

    public static function injectionAuteurDb()
    {
        $pdo=ConnexionDb::getInstance();

        $listeGrillesXml =self::openFolder('tuneXml');

        $query='INSERT INTO auteur(nomAuteur)VALUES(:nomAuteur)';

        foreach($listeGrillesXml as $item){

            if(!is_dir('tuneXml/'.$item)){

                $tune=self::getInfoTune($item);
                $data=[':nomAuteur'=>$tune['auteur']];

               
                if(($res=$pdo->prepare("SELECT id FROM auteur WHERE nomAuteur=:nomAuteur"))){
                    echo 'prepare ';
                    if($res->execute($data)){
                        echo 'execute';
                        if(!($blou=$res->fetch(PDO::FETCH_ASSOC))){
                            echo 'insert';
                            $statement = $pdo->prepare($query);
                            $statement->execute(array(':nomAuteur'=>ucwords($data[':nomAuteur'])));
                        }

                        else{
                            
                        }
                    }

                    else{

                        echo 'impossible execute';
                    }
                    
                }

                else{

                    echo 'impossible de prepare';
                }
                
                 
            }


            else{

                echo ($item.'is folder');
            }
            
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

   public static function getAll():array
    {

        $pdo=new ConnexionDb();
        $data=[];
        $stat=$pdo->query('select distinct auteur from tune order by auteur');
        while($res=$stat->fetch(PDO::FETCH_ASSOC)){

            array_push($data,$res['auteur']);
        }
        return $data;
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

