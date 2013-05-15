<?php

require_once ('VCard.php');

/**
 * Generator for vCards
 * 
 * @author Daniel Theiss <dath@dath.info>
 * @copyright Daniel Theiss 
 * @link https://github.com/dath/phpCalnCards/
 *  
 * @package phpCalnCards 
 * @version 0.7
 * @since 0.1 
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL v3 or later
 *  
 * @see http://www.ietf.org/rfc/rfc6350.txt  RFC 6350: vCard Format Specification   (-> vCard Version 4.0)
 * @see http://www.ietf.org/rfc/rfc2426.txt  RFC 2426: vCard MIME Directory Profile (-> vCard Version 3.0) 
 *    
 */ 
class VCard_Generator {

    /**
     * vCard version for generator
     * 
     * @var string          
     */         
    protected $_version = VCard::VER40;
    
    /**
     * Global array for property grouping
     *      
     * @var array
     */         
    protected $_grouping = null;
    
    /**
     * Generates the vCards as string or vcf-file
     *     
     * @static
     * @param  mixed  vcardObjects  single vCard object or multiple as array
     * @param  string ver           vCard version for output
     * @param  string filename      filename for vcf-file (.vcf will be appended) (optional), if empty a string will returned          
     * 
     * @return mixed  vCards in given version as array or vcf-file if filename is set    
     */              
    public static function generate($vcardObjects = null, $ver = VCard::VER30, $filename = null) {
        global $_version, $_grouping;
        $_version = $ver;
        
        if (!is_array($vcardObjects)) {
            $vcardObjects = array($vcardObjects);
        }

        $vcardString = "";

        foreach ($vcardObjects as $vcardObject) {
            if (!empty($vcardString)) {
                $vcardString .= VCard::LBREAK;
            }
            
            $_grouping = $vcardObject->grouping;
            
            $vcardString .= self::_renderBegin();
            $vcardString .= self::_renderVersion($_version);
            $vcardString .= self::_renderUid($vcardObject);
            $vcardString .= self::_renderSource($vcardObject);
            $vcardString .= self::_renderKind($vcardObject);
            
            $vcardString .= self::_renderFullname($vcardObject);
            $vcardString .= self::_renderName($vcardObject);
            $vcardString .= self::_renderNickname($vcardObject);
            $vcardString .= self::_renderPhoto($vcardObject);
            $vcardString .= self::_renderBirthday($vcardObject);
            $vcardString .= self::_renderAnniversary($vcardObject);
            $vcardString .= self::_renderGender($vcardObject);
            $vcardString .= self::_renderLang($vcardObject);
            
            $vcardString .= self::_renderAddress($vcardObject);
            $vcardString .= self::_renderTelephone($vcardObject);
            $vcardString .= self::_renderEmail($vcardObject);
            $vcardString .= self::_renderInstantmessenger($vcardObject);
            $vcardString .= self::_renderUrl($vcardObject);
            
            $vcardString .= self::_renderTitle($vcardObject);
            $vcardString .= self::_renderRole($vcardObject);
            $vcardString .= self::_renderLogo($vcardObject);
            $vcardString .= self::_renderOrganization($vcardObject);
            
            $vcardString .= self::_renderTimezone($vcardObject);
            $vcardString .= self::_renderGeolocation($vcardObject);
            $vcardString .= self::_renderMailer($vcardObject);
            $vcardString .= self::_renderKey($vcardObject);
            $vcardString .= self::_renderSound($vcardObject);
            $vcardString .= self::_renderCategories($vcardObject);
            $vcardString .= self::_renderNote($vcardObject);

            $vcardString .= self::_renderOthers($vcardObject);
            
            $vcardString .= self::_renderProdid($vcardObject);
            $vcardString .= self::_renderRevision($vcardObject);
            $vcardString .= self::_renderEnd();
        }

        if (!is_null($filename) && $filename != '') {
            header("Content-type: text/directory");
            header("Content-Disposition: attachment; filename=".$filename.".vcf");
            header("Pragma: public");
  	        echo $vcardString;
            return;
        }
        
        return $vcardString;
    }
    
    /**
     * Provides given vCards as vcf-file
     *     
     * @static
     * @param  mixed  vcardObjects  single vCard object or multiple as array
     * @param  string version       vCard version for output
     * @param  string filename      filename for .vcf-file (.vcf will be appended)
     */         
    public static function download($vcardObjects, $version, $filename) {
        self::generate($vcardObjects, $version, $filename);
    }
    
    /*** Helper functions ***/
    
    private static function _makeTypeString($types) {
        global $_version;
        
        $outputString = "";
        $typesList = "";
        $otherTypes = "";
        
        foreach ($types as $type) {
            if (is_null($type) || $type == "") {
              continue;
            } elseif (strpos($type, '=') !== false) {
                $otherTypes .= ";" . $type;
            } elseif ($_version == VCard::VER40 && $type == 'PREF') {
                $otherTypes .= ";PREF=1";
            } else {
                $typesList .= $type . ",";
            }  
        }
        
        if ($otherTypes != "") {
          $outputString .= $otherTypes;
        }
        
        if ($typesList != "") {
          $outputString .= ";TYPE=" . substr($typesList,0,-1);
        }
        
        return $outputString;
    }
    
    private static function _makeGroupPart($arr, $name = "") {
        global $_grouping;

        if (is_array($arr) && isset($arr['group']) && !empty($arr['group'])) {
            return $arr['group'] . ".";
        } elseif ($arr === false && isset($_grouping[$name]) && !empty($_grouping[$name])) {
            return $_grouping[$name] . ".";
        } else
            return "";
    }
    
    private static function _makeOutputString($vcardObject, $key, $key_out, $escape = false) {
        $propertyString = "";
        
        foreach ($vcardObject->$key as $property) {
            $propertyTypeString = "";
            
            if ($property['type']) {
                $propertyTypeString = self::_makeTypeString($property['type']);
            }
            
            if ($escape === true) {
                $property['value'] = self::_escapeValue($property['value']);
            }
            
            $propertyString .= self::_makeGroupPart($property)
                            . $key_out . $propertyTypeString . ":"
                            . $property['value'] . VCard::LBREAK;
        }
        
        return $propertyString;
    }
    
    /**
     * Escapes BACKSLASH, COMMA and SEMICOLON
     */         
    private static function _escapeValue($string) {
        return str_replace(array('\\',',',';'), array('\\\\','\,','\;'), $string);
    }
    
    /*** Render functions for the different properties ***/
    
    protected static function _renderBegin() {
        return "BEGIN:VCARD" . VCard::LBREAK;
    }
    
    protected static function _renderVersion($version) {
        return "VERSION:" . $version . VCard::LBREAK;
    }
    
    protected static function _renderUid($vcardObject) {
        if ($vcardObject->uid) {
            return "UID:" . self::_escapeValue($vcardObject->uid) . VCard::LBREAK;
        }
    }
    
    protected static function _renderSource($vcardObject) {        
        if ($vcardObject->source) {   
            return self::_makeOutputString($vcardObject, 'source', "SOURCE");
        }
    }
    
    protected static function _renderKind($vcardObject) {
        global $_version;
        if ($_version == VCard::VER40 && $vcardObject->kind) {           
            return self::_makeGroupPart(false, 'kind') . "KIND:" . $vcardObject->kind . VCard::LBREAK;
        }
    }

    protected static function _renderFullname($vcardObject) {
        return self::_makeGroupPart(false, 'fullname') . "FN:" . self::_escapeValue($vcardObject->fullname) . VCard::LBREAK;
    }

    protected static function _renderName($vcardObject) {
        $nameString = self::_makeGroupPart(false, 'name')
                    . "N:" . self::_escapeValue($vcardObject->lastname) . ";"
                           . self::_escapeValue($vcardObject->firstname) . ";"
                           . self::_escapeValue($vcardObject->additionalnames) . ";"
                           . self::_escapeValue($vcardObject->nameprefix) . ";"
                           . self::_escapeValue($vcardObject->namesuffix);
        
        // Remove unnecessary semicolons from the end
        while($nameString{strlen($nameString)-1} == ";") {
            $nameString = substr($nameString, 0, -1);
        }
                           
        return $nameString . VCard::LBREAK;
    }
    
    protected static function _renderNickname($vcardObject) {
        global $_version;
        
        if (in_array($_version, array(VCard::VER30, VCard::VER40)) && $vcardObject->nickname) {
            return self::_makeGroupPart(false, 'nickname') . "NICKNAME:" . $vcardObject->nickname . VCard::LBREAK;
        }
    }
    
    protected static function _renderPhoto($vcardObject) {
        if ($vcardObject->photo) {
            return self::_makeOutputString($vcardObject, 'photo', "PHOTO");
        }
    }

    protected static function _renderBirthday($vcardObject) {
        if ($vcardObject->birthday) {
            return self::_makeGroupPart(false, 'birthday') . "BDAY:" . $vcardObject->birthday . VCard::LBREAK;
        }
    }
    
    protected static function _renderAnniversary($vcardObject) {
        global $_version;
        
        if ($_version == VCard::VER40 && $vcardObject->anniversary) {
            return self::_makeGroupPart(false, 'anniversary') . "ANNIVERSARY:" . $vcardObject->anniversary . VCard::LBREAK;
        }
    }
    
    protected static function _renderGender($vcardObject) {
        global $_version;
        
        if ($vcardObject->gender) {
            if ($_version == VCard::VER40) {
              return self::_makeGroupPart(false, 'gender') . "GENDER:" . $vcardObject->gender{0} . VCard::LBREAK;
            }
            
            return self::_makeGroupPart(false, 'gender') . "X-GENDER:" . $vcardObject->gender . VCard::LBREAK;
        }
    }
    
    protected static function _renderLang($vcardObject) {
        global $_version;
        
        if ($_version == VCard::VER40 && $vcardObject->lang) {
            return self::_makeOutputString($vcardObject, 'lang', "LANG");
        }
    }
    
    protected static function _renderAddress($vcardObject) {
        if ($vcardObject->address) {
            $addressString = "";
            
            foreach ($vcardObject->address as $address) {
                $addressTypeString = "";
                
                if ($address['type']) {
                    $addressTypeString = self::_makeTypeString($address['type']);
                }
                
                $addressString .= self::_makeGroupPart($address)
                                . "ADR" . $addressTypeString . ":"
                                        . self::_escapeValue($address['pobox']) . ";"
                                        . self::_escapeValue($address['extendedaddress']) . ";"
                                        . self::_escapeValue($address['street']) . ";"
                                        . self::_escapeValue($address['city']) . ";"
                                        . self::_escapeValue($address['region']) . ";"
                                        . self::_escapeValue($address['postalcode']) . ";"
                                        . self::_escapeValue($address['country']) . VCard::LBREAK;
            }
            
            return $addressString;
        }
    }

    protected static function _renderTelephone($vcardObject) {
        if ($vcardObject->telephone) {
            return self::_makeOutputString($vcardObject, 'telephone', "TEL");
        }
    }

    protected static function _renderEmail($vcardObject) {
        if ($vcardObject->email) {
            return self::_makeOutputString($vcardObject, 'email', "EMAIL", true);
        }
    }

    protected static function _renderInstantmessenger($vcardObject) {
        if ($vcardObject->im) {
            $imString = "";
            
            foreach ($vcardObject->im as $im) {
                $imTypeString = "";
                
                if ($im['type']) {
                    $imTypeString = self::_makeTypeString($im['type']);
                }
                
                $imString .= self::_makeGroupPart($im)
                             . $im['messenger'] . $imTypeString . ":"
                             . $im['value'] . VCard::LBREAK;
            }
            
            return $imString;
        }
    }
    
    protected static function _renderUrl($vcardObject) {
        if ($vcardObject->url) {
            return self::_makeOutputString($vcardObject, 'url', "URL");
        }
    }

    protected static function _renderTitle($vcardObject) {
        if ($vcardObject->title) {
            return self::_makeGroupPart(false, 'title') . "TITLE:" . self::_escapeValue($vcardObject->title) . VCard::LBREAK;
        }
    }

    protected static function _renderRole($vcardObject) {
        if ($vcardObject->role) {
            return self::_makeGroupPart(false, 'role') . "ROLE:" . self::_escapeValue($vcardObject->role) . VCard::LBREAK;
        }
    }

    protected static function _renderLogo($vcardObject) {
        if ($vcardObject->logo) {
            return self::_makeOutputString($vcardObject, 'logo', "LOGO");
        }
    }
    
    protected static function _renderOrganization($vcardObject) {
        if ($vcardObject->organization || $vcardObject->department || $vcardObject->subdepartment) {
            return self::_makeGroupPart(false, 'organization')
                   . "ORG:" . self::_escapeValue($vcardObject->organization) . ";"
                            . self::_escapeValue($vcardObject->department) . ";"
                            . self::_escapeValue($vcardObject->subdepartment) . VCard::LBREAK;
        }
    }
    
    protected static function _renderTimezone($vcardObject) {
        if ($vcardObject->timezone) {
            return _makeGroupPart(false, 'timezone') . "TZ:" . $vcardObject->timezone . VCard::LBREAK;
        }
    }
    
    protected static function _renderGeolocation($vcardObject) {
        if ($vcardObject->geolocation) {
            return self::_makeGroupPart(false, 'geolocation') . "GEO:" . $vcardObject->geolocation . VCard::LBREAK;
        }
    }
    
    protected static function _renderMailer($vcardObject) {
        global $_version;
        
        if (in_array($_version, array(VCard::VER21, VCard::VER30)) && $vcardObject->mailer) {
            return self::_makeGroupPart(false, 'mailer') . "MAILER:" . self::_escapeValue($vcardObject->mailer) . VCard::LBREAK;
        }
    }
    
    protected static function _renderKey($vcardObject) {
        if ($vcardObject->key) {
            return self::_makeOutputString($vcardObject, 'key', "KEY");
        }
    }
    
    protected static function _renderSound($vcardObject) {
        if ($vcardObject->sound) {
            return self::_makeOutputString($vcardObject, 'sound', "SOUND");
        }
    }
    
    protected static function _renderCategories($vcardObject) {
        global $_version;
        
        if (in_array($_version, array(VCard::VER30, VCard::VER40)) && $vcardObject->categories) {
            $categoriesString = "";
            
                        
            foreach ($vcardObject->categories as $category) {
                $categoriesString .= self::_escapeValue($category['value']) . ","; 
            }
            
            return self::_makeGroupPart(false, 'categories') . "CATEGORIES:" . substr($categoriesString,0,-1) . VCard::LBREAK;
        }
    }
    
    protected static function _renderNote($vcardObject) {
        if ($vcardObject->note) {
            return self::_makeGroupPart(false, 'note') . "NOTE:" . self::_escapeValue($vcardObject->note) . VCard::LBREAK;
        }
    }
    
    protected static function _renderOthers($vcardObject) {
        if ($vcardObject->others) {
            $othersString = "";
            
            foreach ($vcardObject->others as $other) {
                $otherTypeString = "";
                
                if ($other['type']) {
                    $otherTypeString = self::_makeTypeString($other['type']);
                }
                
                $othersString .= self::_makeGroupPart($other)
                               . $other['key'] . $otherTypeString . ":"
                               . $other['value'] . VCard::LBREAK; 
            }
            
            return $othersString;
        }
    }
    
    protected static function _renderProdid($vcardObject) {
        global $_version;
        
        if (in_array($_version, array(VCard::VER30, VCard::VER40)) && $vcardObject->prodid) {
            return "PRODID:" . self::_escapeValue($vcardObject->prodid) . VCard::LBREAK;
        }
    }
    
    protected static function _renderRevision($vcardObject) {
        if ($vcardObject->revision) {
            return "REV:" . $vcardObject->revision . VCard::LBREAK;
        }
    }
    
    protected static function _renderEnd() {
        return "END:VCARD";
    }
    
}

?>