<?php
use Converter\Converter;

require ('Converter.php');
require('digXML.php');

//digXML::digXML('tuneOdt','tuneXml');

//digXML::injectionDb();




$dig = new digXML;

$listeGrille=$dig::openFolder('tuneOdt');

// $converter = new Converter;

// //$res=Converter::clean($listeGrille);

// foreach($listeGrille as $grille){

//     $converter::convert('tuneOdt/'.$grille,'tunePdf');

// }



?>

