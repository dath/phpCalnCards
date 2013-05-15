<?php

/**
 * Examples for vCards
 * 
 * @author Daniel Theiss <dath@dath.info>
 * @copyright Daniel Theiss 
 * @link http://www.dath.info/phpCalnCards/
 *  
 * @package phpCalnCards 
 * @version 0.7
 * @since 0.7 
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3 or later 
 *    
 */
   
require_once('VCard.php');
require_once('VCard_Generator.php');
require_once('VCard_Parser.php');
    
## Create a vCard
$vCard = new VCard();
$vCard->setFullname('John Doe');
$vCard->setFirstname('John');
$vCard->setLastname('Doe');
$vCard->addEmail('john.doe@example.org');
$vCard->addPhonenumber('1-234-567890',VCard::TELEPHONEHOME);
$vCard->addAddress(array('street' => 'Examplestreet 123',
                         'city' => 'City',
                         'region' => 'Somestate',
                         'postalcode' => '4567',
                         'country' => 'Country'));
$vCard->addAddress(array('pobox' => '639104',
                         'extendedaddress' => '',
                         'street' => 'Examplestreet 456',
                         'city' => 'City',
                         'region' => 'Somestate',
                         'postalcode' => '7890',
                         'country' => 'Country',
                         'type' => array(VCard::ADDRESSWORK, VCard::ADDRESSPOSTAL)));
/* Add multiple URLs at once */
$vCard->addUrl(array(array('value' => "http://www.example.com",
                           'type' => array(VCard::URLWORK)),
                     array('value' => "http://www.example.org",
                            'type' => array(VCard::URLPREF, VCard::URLWORK))));
$vCard->setNote("This is a short note.");
$vCard->setBirthday("1980-10-10");
                         
## Save vCard as example.vcf
if (isset($_GET['download'])) {
    VCard_Generator::download($vCard,'4.0','example');
}

## Generate vCard as string
$vCardString = VCard_Generator::generate($vCard,'4.0');
echo "<pre>" . $vCardString . "</pre><hr />";

## Parse a vCard and print it
$vCardString2 = "BEGIN:VCARD\r\nVERSION:4.0\r\nFN:John Doe\r\nN:Doe;John\r\nADR:;;Examplestreet 123;City;Somestate;4567;Country\r\nTEL;TYPE=HOME:1-234-567890\r\nEMAIL:john.doe@example.org\r\nEND:VCARD\r\n";
$parser = new VCard_Parser($vCardString2);
$cardObject = $parser->parse();
$card = $cardObject[0];

$vCardString3 = VCard_Generator::generate($card,'4.0');
echo "<pre>" . $vCardString3 . "</pre><hr />";        
       
?>