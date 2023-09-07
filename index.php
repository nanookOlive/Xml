<?php

require('digXML.php');

$_text=[];
$content=digXML::readXml('Dans la salle du bar-tabac.xml');


foreach($content as $item){

    
        if($item["name"]=='#text' && $item['value'] !=' ' ){
            array_push($_text,$item['value']);
        }
       
    
}

var_dump($_text);