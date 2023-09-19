<?php
use Converter\Converter;

require ('Converter.php');
require('digXML.php');


//export du XML de chaque ODT pour récupèrer l'auteur de chaque grille, puis export dans la base données
$dig = new digXML;

// digXML::digXML('tuneOdt','tuneXml');

// digXML::injectionDb();





//conversion de toutes Grilles en .odt vers .pdf

$converter = new Converter;
//on enelève les ' et les espaces dans les noms 

// $listeGrille=$dig::openFolder('tuneOdt');

// $res=Converter::clean($listeGrille,'tuneOdt','tuneOdt');

// $listeGrille=$dig::openFolder('tuneOdt');

// foreach($listeGrille as $grille){

//     $converter::convert('tuneOdt/'.$grille,'tunePdf');

// }
// $listeGrille=$dig::openFolder('tunePdf');

// $converter::readable($listeGrille,'tunePdf','tunePdf');

// $listeGrille=$dig::openFolder('tuneOdt');

// $converter::readable($listeGrille,'tuneOdt','tuneOdt');
?>

