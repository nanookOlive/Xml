<?php

require('digXML.php');

$xml=new digXML();
$res=$xml->getInfoTune('Between the bars.xml');
echo $res['titre'].' de '.$res["auteur"] ;
