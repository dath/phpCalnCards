# phpCalnCards #

**phpCalnCards** provides simple parser and generators for iCalendar and vCard in PHP.

**Current version supports only vCard! Support for iCalendar is planned.**

---

## vCards ##
This section will describe how to handle with vCards.
See also `examples_vcard.php` for more examples.

### How to create a vCard object? ###
1. Include `VCard.php` to your script
2. Instantiate a vCard
3. Set properties

** Example **

    require_once('VCard.php');
    
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
                             
 
### How to generate a vCard? ###
1. Include `VCard_Generator.php` to your script
2. Call `VCard_Generator::generate($vCard,$version);`, where *$vCard* is the vCard object and *$version* the vCard version (2.1, 3.0 or 4.0)
3. To save the vCard directly as .vcf-file just add the filename as last parameter or call `VCard_generator::download($vCard,$version,$filename);`

** Example **
    
    require_once('VCard_Generator.php');
    
    ## Generate a vCard (Version 4.0)
    $vCardString = VCard_Generator::generate($vCard,'4.0');
    
    ## Save a vCard as example.vcf
    VCard_Generator::generate($vCard,'4.0','example');
    
### How to parse vCards? ###
1. Include `VCard_Parser.php` to your script    
2. Instantiate a new VCard_Parser with vCardString or .vcf-file
3. Call `$parser->parse();` which returns an array of vCard objects
4. Get the first vCard via index 0 (zero)

** Example **

    require_once('VCard_Parser.php');
    
    ## a vCard as string
    $vCardString = "BEGIN:VCARD\r\nVERSION:4.0\r\nFN:John Doe\r\nN:Doe;John\r\nADR:;;Examplestreet 123;City;Somestate;4567;Country\r\nTEL;TYPE=HOME:1-234-567890\r\nEMAIL:john.doe@example.org\r\nEND:VCARD\r\n";
    
    ## Parse a vCard
    $parser = new VCard_Parser($vCardString);
    $cardObject = $parser->parse();
    $card = $cardObject[0];

### More Information ###
A more detailed documentation is planned. To find out which functions you can use at the moment, please look into the source code.

#### See also ####

* [RFC 6350: vCard Format Specification](http://www.ietf.org/rfc/rfc6350.txt) (for vCard Version 4.0)
* [RFC 2426: vCard MIME Directory Profile](http://www.ietf.org/rfc/rfc2426.txt) (for vCard Version 3.0)

---

## Planned / Under Development ##

* Detailed documentation or wiki
* Automatic tests
* Parser for iCalendar
* Generator for iCalendar

## Change Log ##
 Version | Note 
:--------|:-----
 **v0.7** (2013/05/15) | Online Release at github
 **v0.2 to v0.6**      | Add support for different vCard properties
 **v0.1** (2013/04/30) | Initial

## License Information ##

Copyright (C) 2013  Daniel Theiss

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
