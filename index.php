<?php

require('digXML.php');


$res=digXML::getInfoTune('Little Boxes.xml');
echo $res['titre'].' de '.$res["auteur"] ;
ConnexionDB::getInstance();