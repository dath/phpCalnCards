<?php

require_once ('VCard.php');

/**
 * Parser for vCards
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
class VCard_Parser {
    
    protected $_content = null;
    protected $_vcardCacheObject = null;

    /**
     * Constructor
     *      
     * @param string filename path to vCard file or vCard as string    
     */         
    public function __construct($filename) {
        if (is_file($filename)) {
            $this->_content = file_get_contents($filename);
        } else {
            $this->_content = $filename;
        }
    }

    /**
     * Parses vCards and returns vCard Object
     *
     * @return vCard object     
     */
    public function parse() {
        $vcardObjectArray = array();
        $keys = array("VERSION" => 'version',
                      "UID" => 'uid',
                      "SOURCE" => 'source',
                      "KIND" => 'kind',
                      "FN" => 'fullname',
                      "N" => 'name',
                      "NICKNAME" => 'nickname',
                      "PHOTO" => 'photo',
                      "BDAY" => 'birthday',
                      "ANNIVERSARY" => 'anniversary',
                      "GENDER" => 'gender',
                      "X-GENDER" => 'gender',
                      "LANG" => 'lang',
                      "ADR" => 'address',
                      "TEL" => 'telephone',
                      "EMAIL" => 'email',
                      "IMPP" => 'im',
                      "X-AIM" => 'im',
                      "X-GADUGADU" => 'im',
                      "X-GOOGLE-TALK" => 'im',
                      "X-ICQ" => 'im',
                      "X-JABBER" => 'im',
                      "X-MSN" => 'im',
                      "X-SKYPE" => 'im',
                      "X-SKYPE-USERNAME" => 'im',
                      "X-TWITTER" => 'im',
                      "X-YAHOO" => 'im',
                      "URL" => 'url',
                      "TITLE" => 'title',
                      "ROLE" => 'role',
                      "LOGO" => 'logo',
                      "ORG" => 'organization',
                      "TZ" => 'timezone',
                      "GEO" => 'geo',
                      "MAILER" => 'mailer',
                      "KEY" => 'key',
                      "SOUND" => 'sound',
                      "CATEGORIES" => 'categories',
                      "NOTE" => 'note',
                      "PRODID" => 'prodid',
                      "REV" => 'revision'
                     );

        /* Unfolding like defined in RFC 6350 3.2 */  
        $this->_content = preg_replace("/\n(?:[ \t])/", "", $this->_content);
        
        $lines = explode(VCard::LBREAK, $this->_content);

        foreach ($lines as $line) {
            $line = trim($line);
            
            if (strtoupper($line) == "BEGIN:VCARD") {
                $this->_vcardCacheObject = new VCard();
            } elseif (strtoupper($line) == "END:VCARD") {
                $vcardObjectArray[] = $this->_vcardCacheObject;
            } elseif ($line != null) {
                $type = '';
                $value = '';
                $group = null;
                
                list ($type, $value) = explode(':', $line, 2);
                $types = explode(';', $type);
                
                // save group if exist
                if (strpos($types[0],'.') !== false) {
                  list($group,$types[0]) = explode('.', $types[0], 2);
                }
                
                $key = strtoupper($types[0]);
                
                array_shift($types);
                $i = 0;
                
                foreach ($types as $type) {
                    if (strpos(strtolower($type), 'base64')) {
                        $value = base64_decode($value);
                        unset($types[$i]);
                    } elseif (strpos(strtolower($type), 'encoding=b')) {
                        $value = base64_decode($value);
                        unset($types[$i]);
                    } elseif (strpos(strtolower($type), 'quoted-printable')) {
                        $value = quoted_printable_decode($value);
                        unset($types[$i]);
                    } elseif (strpos(strtolower($type), 'charset=') === 0) {
                        try {
                            $value = mb_convert_encoding($value, "UTF-8", substr($type, 8));
                        } catch (Exception $e) {
                        }
                        
                        unset($types[$i]);
                    }
                    
                    $i++;
                }
                
                if (in_array(strtoupper($key), array_keys($keys))) {
                    if ($keys[$key] == 'im') {
                        call_user_func(array($this, "_parse" . ucfirst($keys[$key])), $value, $key, $types, $group);
                    } elseif (isset($types[0]) && isset($group)) {
                        call_user_func(array($this, "_parse" . ucfirst($keys[$key])), $value, $types, $group);
                    } elseif (isset($types[0])) {
                        call_user_func(array($this, "_parse" . ucfirst($keys[$key])), $value, $types);
                    } elseif (isset($group)) {
                        call_user_func(array($this, "_parse" . ucfirst($keys[$key])), $value, array(), $group);
                    } else {
                        call_user_func(array($this, "_parse" . ucfirst($keys[$key])), $value);
                    }
                } else {
                    if (isset($group)) {
                      call_user_func(array($this, "_parseOthers"), $key, $value, $types, $group);
                    } else {
                      call_user_func(array($this, "_parseOthers"), $key, $value, $types);
                    }
                }
            }
        }

        return $vcardObjectArray;
    }
    
    /*** Helper functions ***/
    
    private static function _unescapeValue($string) {
        return str_replace(array('\\\\','\,','\;'), array('\\',',',';'), $string);
    }
    
    private static function _explodeTypes($args) {
        $types = array();
        
        foreach ($args as $typeCache) {
            $typeCache = strtoupper($typeCache);
            if (strpos($typeCache, 'TYPE=') === 0) {
                $types = array_merge($types, explode(',', substr($typeCache, 5)));
            } else {
                $types[] = $typeCache;
            }
        }
        
        return $types;
    }
    
    /*** Parse functions for the different properties ***/
    
    protected function _parseVersion($value) {
        $this->_vcardCacheObject->changeVersion($value);
    }
    
    protected function _parseUid($value, $args = array()) {
        $this->_vcardCacheObject->setUid(self::_unescapeValue($value));
    }
    
    protected function _parseSource($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addSource($value, self::_explodeTypes($args), $group);
    }
    
    protected function _parseKind($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setKind($value, self::_explodeTypes($args));
        $this->_vcardCacheObject->addToGrouping('kind', $group);
    }
    
    protected function _parseFullname($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setFullname(self::_unescapeValue($value));
        $this->_vcardCacheObject->addToGrouping('fullname', $group);
    }

    protected function _parseName($value, $args = array(), $group = null) {
        $value = explode(';', $value);
        
        if (isset($value[0])) {
            $this->_vcardCacheObject->setLastname(self::_unescapeValue($value[0]));
        }
        if (isset($value[1])) {
            $this->_vcardCacheObject->setFirstname(self::_unescapeValue($value[1]));
        }
        if (isset($value[2])) {
            $this->_vcardCacheObject->setAdditionalNames(self::_unescapeValue($value[2]));
        }
        if (isset($value[3])) {
            $this->_vcardCacheObject->setNamePrefix(self::_unescapeValue($value[3]));
        }
        if (isset($value[4])) {
            $this->_vcardCacheObject->setNameSuffix(self::_unescapeValue($value[4]));
        }
        
        $this->_vcardCacheObject->addToGrouping('name', $group);
    }

    protected function _parseNickname($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setNickname($value);
        $this->_vcardCacheObject->addToGrouping('nickname', $group);
    }
    
    protected function _parsePhoto($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addPhoto(self::_unescapeValue($value), self::_explodeTypes($args), $group);
    }
    
    protected function _parseBirthday($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setBirthday($value);
        $this->_vcardCacheObject->addToGrouping('birthday', $group);
    }
    
    protected function _parseAnniversary($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setAnniversary($value);
        $this->_vcardCacheObject->addToGrouping('anniversary', $group);
    }

    protected function _parseGender($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setGender($value);
        $this->_vcardCacheObject->addToGrouping('gender', $group);
    }

    protected function _parseLang($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addLang($value, self::_explodeTypes($args), $group);
    }

    protected function _parseAddress($value, $args = array(), $group = null) {
        $type = self::_explodeTypes($args);
        $value = explode(';', $value);
        $this->_vcardCacheObject->addAddress(array(
                        'pobox' => (isset($value['0']) ? self::_unescapeValue($value['0']) : ''),
                        'extendedaddress' => (isset($value['1']) ? self::_unescapeValue($value['1']) : ''),
                        'street' => (isset($value['2']) ? self::_unescapeValue($value['2']) : ''),
                        'city' => (isset($value['3']) ? self::_unescapeValue($value['3']) : ''),
                        'region' => (isset($value['4']) ? self::_unescapeValue($value['4']) : ''),
                        'postalcode' => (isset($value['5']) ? self::_unescapeValue($value['5']) : ''),
                        'country' => (isset($value['6']) ? self::_unescapeValue($value['6']) : ''),
                        'type' => $type,
                        'group' => $group));
    }

    protected function _parseTelephone($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addPhonenumber($value, self::_explodeTypes($args), $group);
    }

    protected function _parseEmail($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addEmail(self::_unescapeValue($value), self::_explodeTypes($args), $group);
    }

    protected function _parseIm($value, $messenger, $args = array(), $group = null) {
        $this->_vcardCacheObject->addInstantmessenger($value, $messenger, self::_explodeTypes($args), $group);
    }
    
    protected function _parseUrl($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addUrl($value, self::_explodeTypes($args), $group);
    }

    protected function _parseTitle($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setTitle(self::_unescapeValue($value));
        $this->_vcardCacheObject->addToGrouping('title', $group);
    }
    
    protected function _parseRole($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setRole(self::_unescapeValue($value));
        $this->_vcardCacheObject->addToGrouping('role', $group);
    }
    
    protected function _parseLogo($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addLogo(self::_unescapeValue($value), self::_explodeTypes($args), $group);
    }
    
    protected function _parseOrganization($value, $args = array(), $group = null) {
        $value = explode(';', $value);
        
        if (isset($value[0])) {
            $this->_vcardCacheObject->setOrganization(self::_unescapeValue($value[0]));
            $this->_vcardCacheObject->addToGrouping('organization', $group);
        }
        if (isset($value[1])) {
            $this->_vcardCacheObject->setDepartment(self::_unescapeValue($value[1]));
        }
        if (isset($value[2])) {
            $this->_vcardCacheObject->setSubDepartment(self::_unescapeValue($value[2]));
        }
    }

    protected function _parseTimezone($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setTimezone($value);
        $this->_vcardCacheObject->addToGrouping('timezone', $group);
    }
    
    protected function _parseGeo($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setGeolocation($value);
        $this->_vcardCacheObject->addToGrouping('geolocation', $group);
    }

    protected function _parseMailer($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setMailer(self::_unescapeValue($value));
        $this->_vcardCacheObject->addToGrouping('mailer', $group);
    }

    protected function _parseKey($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addKey(self::_unescapeValue($value), self::_explodeTypes($args), $group);
    }
    
    protected function _parseSound($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->addSound(self::_unescapeValue($value), self::_explodeTypes($args), $group);
    }
    
    protected function _parseCategories($value, $args = array(), $group = null) {
        $categories = explode(',', self::_unescapeValue($value));
        
        foreach ($categories as $category) {
          $this->_vcardCacheObject->addCategory($category);
        }
        
        $this->_vcardCacheObject->addToGrouping('categories', $group);
    }
    
    protected function _parseNote($value, $args = array(), $group = null) {
        $this->_vcardCacheObject->setNote(self::_unescapeValue($value));
        $this->_vcardCacheObject->addToGrouping('note', $group);
    }
    
    protected function _parseOthers($value, $key, $args = array(), $group = null) {
        $this->_vcardCacheObject->addOthers($value, $key, self::_explodeTypes($args), $group);
    }
    
    protected function _parseProdid($value, $args = array()) {
        $this->_vcardCacheObject->setProdid(self::_unescapeValue($value));
    }

    protected function _parseRevision($value, $args = array()) {
        $this->_vcardCacheObject->setRevision($value);
    }
        
}

?>