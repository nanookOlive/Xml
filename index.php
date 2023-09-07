<?php

require('digXML.php');

digXML::digXML('tuneOdt','tuneXml');
//digXML::injectionDb();

//rmdir('tuneXml/Thumbnails');

//unlink('tuneXml/Thumbnails/thumbnail.png');

// $path='tuneXml/Configurations2';
// $array=['.','..'];


// foreach(scandir($path) as $item){

//     if(is_dir($path.'/'.$item) ){

//         if(!in_array($item,$array)){

//             $array2=(scandir($path.'/'.$item));

//             foreach($array2 as $item2){

//                 echo $item2;
//             }
            

//         }
//     }
// }




