<?php

/**
 * Class for vCards
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
 */   
class VCard {
    
    const LBREAK = "\r\n";
    const VER21 = '2.1',
          VER30 = '3.0',
          VER40 = '4.0';
    
    protected $_version = self::VER40;

    /**
     * @var array (name => groupname)
     */         
    protected $_grouping = array();
    
    protected $_uid = null,
              $_fullname = null,
              $_lastname = null,
              $_firstname = null,
              $_additionalnames = null,
              $_nameprefix = null,
              $_namesuffix = null,
              $_nickname = null,        
              $_birthday = null,
              $_anniversary = null,
              $_title = null,
              $_role = null,
              $_organization = null,
              $_department = null,
              $_subdepartment = null, 
              $_timezone = null,
              $_geolocation = null,
              $_mailer = null,
              $_note = null,
              $_prodid = null,
              $_revision = null;
    
    /**
     * @var array(string)
     */         
    protected $_categories = array();
     
    /**
     * @var array array(value, type=array(), group=null)
     */
    protected $_source = array();
    const SOURCEPREF = 'PREF';
    protected $_sourceConstantArray = array(self::SOURCEPREF);
    
    /**
     * @var string
     */         
    protected $_kind = null;
    const KINDGROUP = 'group',
          KINDINDIVIDUAL = 'individual',
          KINDLOCATION = 'location',
          KINDORG = 'org';
    protected $_kindConstantArray = array(self::KINDGROUP,
                                          self::KINDINDIVIDUAL,
                                          self::KINDLOCATION,
                                          self::KINDORG
                                         );
                                         
    /**
     * @var array array(value, type=array(), group=null)
     */
    protected $_photo = array(),
              $_logo  = array();
    
    /**
     * @var string
     */                                                  
    protected $_gender = null;
    const GENDERFEMALE = 'Female',
          GENDERMALE = 'Male';
    /**
     * @var array array(value, type=array(), group=null)
     */
    protected $_lang = array();
    const LANGHOME = 'HOME',
          LANGPREF = 'PREF',
          LANGWORK = 'WORK';
    protected $_langConstantArray = array(self::LANGHOME,
                                          self::LANGPREF,
                                          self::LANGWORK
                                         );   
    /**
     * @var array array(pobox, extendedaddress, street, city, region, postalcode, country, type=array(), group=null)
     */
    protected $_address = array();
    const ADDRESSDOMESTIC = 'DOM',
          ADDRESSHOME = 'HOME',
          ADDRESSINTERNATIONAL = 'INTL',
          ADDRESSPARCEL = 'PARCEL',
          ADDRESSPOSTAL = 'POSTAL',
          ADDRESSPREF = 'PREF',
          ADDRESSWORK = 'WORK';
    protected $_addressConstantArray = array(self::ADDRESSDOMESTIC,
                                             self::ADDRESSHOME,
                                             self::ADDRESSINTERNATIONAL,
                                             self::ADDRESSPARCEL,
                                             self::ADDRESSPOSTAL,
                                             self::ADDRESSPREF,
                                             self::ADDRESSWORK
                                            );
    /**
     * @var array array(value, type=array(), group=null)
     */
    protected $_telephone = array();
    const TELEPHONECAR = 'CAR',
          TELEPHONECELL = 'CELL',
          TELEPHONEFAX = 'FAX',
          TELEPHONEHOME = 'HOME',
          TELEPHONEIPHONE = 'IPHONE',
          TELEPHONEMSG = 'MSG',
          TELEPHONEOTHER = 'OTHER',
          TELEPHONEPAGER = 'PAGER',
          TELEPHONEPREF = 'PREF',
          TELEPHONEVIDEO = 'VIDEO',
          TELEPHONEVOICE = 'VOICE',
          TELEPHONEWORK = 'WORK';    
    protected $_telephoneConstantArray = array(self::TELEPHONECAR,
                                               self::TELEPHONECELL,
                                               self::TELEPHONEFAX,
                                               self::TELEPHONEHOME,
                                               self::TELEPHONEIPHONE,
                                               self::TELEPHONEMSG,
                                               self::TELEPHONEOTHER,
                                               self::TELEPHONEPAGER,
                                               self::TELEPHONEPREF,
                                               self::TELEPHONEVIDEO,
                                               self::TELEPHONEVOICE,
                                               self::TELEPHONEWORK
                                              );
    /**
     * @var array array(value, type=array(), group=null)
     */
    protected $_email = array();
    const EMAILHOME = 'HOME',
          EMAILINTERNET = 'INTERNET',
          EMAILOTHER = 'OTHER',
          EMAILPREF = 'PREF',
          EMAILWORK= 'WORK',
          EMAILX400 = 'X400';
    protected $_emailConstantArray = array(self::EMAILHOME,
                                           self::EMAILINTERNET,
                                           self::EMAILOTHER,
                                           self::EMAILPREF,
                                           self::EMAILWORK,
                                           self::EMAILX400
                                          );
    /**
     * @var array array(value, type=array(), messenger=null, group=null)
     */
    protected $_im = array();
    const IMHOME = 'HOME',
          IMOTHER = 'OTHER',
          IMPREF = 'PREF',
          IMWORK = 'WORK';
    const IMPP = 'IMPP';
    const IMAIM = 'X-AIM',
          IMGADUGADU = 'X-GADUGADU',
          IMGTALK = 'X-GOOGLE-TALK',
          IMICQ = 'X-ICQ',
          IMJABBER = 'X-JABBER',
          IMMSN = 'X-MSN',
          IMSKYPE = 'X-SKYPE',
          IMSKYPEALTERNATIVE = 'X-SKYPE-USERNAME',
          IMTWITTER = 'X-TWITTER',
          IMYAHOO = 'X-YAHOO';
    protected $_imConstantArray = array(self::IMHOME,
                                        self::IMOTHER,
                                        self::IMPREF,
                                        self::IMWORK
                                       );
    protected $_imMessengerConstantArray = array(self::IMPP,
                                                 self::IMAIM,
                                                 self::IMGADUGADU,
                                                 self::IMGTALK,
                                                 self::IMICQ,
                                                 self::IMJABBER,
                                                 self::IMMSN,
                                                 self::IMSKYPE,
                                                 self::IMSKYPEALTERNATIVE,
                                                 self::IMTWITTER,
                                                 self::IMYAHOO
                                                );                                               
    /**
     * @var array array(value, type=array(), group=null)
     */
    protected $_url = array();
    const URLHOME = 'HOME',
          URLOTHER = 'OTHER',
          URLPREF = 'PREF',
          URLWORK= 'WORK';
    protected $_urlConstantArray = array(self::URLHOME,
                                         self::URLOTHER,
                                         self::URLPREF,
                                         self::URLWORK
                                        );
    /**
     * @var array array(value, type=array(), group=null)
     */
     protected $_key   = array(),
               $_sound = array();
    
    /**
     * @var array array(key, value, type=array(), group=null)
     */
    protected $_others = array();


    /*** Magic Functions ***/
    
    public function __call($function, $args) {
        $function = strtolower($function);
        $property = substr($function, 3);

        if (substr($function, 0, 3) == 'set' && property_exists($this, "_" . $property) && !is_array($this->{"_" . $property})) {
            if (is_string($args[0]) || is_int($args[0])) {
                $this->$property = $args[0];
                return $this;
            }
        }

        throw new Exception("Unknown property $property");
    }

    public function __get($name) {
        if (property_exists($this, "_" . $name)) {
            return $this->{"_" . $name};
        } else {
            throw new Exception("Unknown property $name");
        }
    }

    public function __set($name, $value) {
        if (property_exists($this, "_" . $name) && !is_array($this->{"_" . $name})) {
            $this->{"_" . $name} = $value;
        } else {
            throw new Exception("Unknown property $name");
        }
    }

    /*** Helper functions ***/
    
    private static function _transformSingleToArray($arr, $type) {
        if (!is_array($arr) && is_string($arr)) {
            $cacheArray = array();
            $cacheArray[0]['value'] = $arr;
            
            if (is_array($type)) {
                $cacheArray[0]['type'] = $type;
            } else {
                $cacheArray[0]['type'][] = $type;
            }
            
            $arr = $cacheArray;
        }
                
        return $arr;
    }
    
    private function _addValuesLoop($key, $arr, $group = null) {
        foreach ($arr as $value) {
            $typeArray = array();
            
            if (is_string($value['type'])) {
              $value['type'] = array($value['type']);
            }
            
            foreach ($value['type'] as $type) {
                if (isset($this->{"_" . $key . "ConstantArray"}) && in_array($type, $this->{"_" . $key . "ConstantArray"})) {
                    $typeArray[] = $type;
                } elseif ($this->_version == self::VER40 && strpos($type, 'PREF=') === 0) {
                    $typeArray[] = $type;
                } elseif (!isset($this->{"_" . $key . "ConstantArray"})) {
                    $typeArray[] = $type;
                }
            }
            
            $this->{"_" . $key}[] = array('value' => $value['value'], 'type' => $typeArray, 'group' => $group);
        }
    }
    
    
    /**
     * Changes Version of current vCard-Object.
     *      
     * @param string version  vCard Version
     *      
     * @return this          
     */         
    public function changeVersion($version) {
        if ($version != "") {
            if ($version == "2.1") {
              $this->_version = self::VER21;
            } elseif ($version == "3.0") {
              $this->_version = self::VER30;
            } elseif ($version == "4.0") {
              $this->_version = self::VER40;
            } else {
              throw new Exception("Unknown vCard version $name");
            }
        } else {
            throw new Exception("Empty Version!");
        }
        
        return $this;
    }
    
    /**
     * Set kind of current vCard-Object.
     *      
     * @param string value kind of vCard
     *      
     * @return this          
     */
    public function setKind($value) {
        if (in_array($value, $this->_kindConstantArray)) {
          $this->_kind = $value;
        } else {
          throw new Exception("Unknown KIND property value $value");
        }
        
        return $this;
    }
    
    /**
     * Saves the groupname of specified property.
     * 
     * @param string name       property name
     * @param string groupname  groupname of property              
     */         
    public function addToGrouping($name, $groupname) {
        if (empty($name) || empty($groupname)) {
            return;
        } elseif (in_array($name, array('name','categories')) || (property_exists($this, "_" . $name) && !is_array($this->{"_" . $name}))) {
            $this->_grouping[$name] = $groupname;
        } else {
            throw new Exception("Unknown property $name");
        }
    }
    
    /*** Public functions for adding values ***/
    
    /**
     * Adds a source to vCard.
     * 
     * @param mixed   sourceArray a single source or multiple sources as array( array(value, type) )
     * @param array   type        types to given source (optional)
     * @param string  group       groupname of source (optional)
     * 
     * @return this
     */
    public function addSource($sourceArray, $type = null, $group = null) {
        $sourceArray = self::_transformSingleToArray($sourceArray, $type);
        $this->_addValuesLoop('source', $sourceArray, $group);
        
        return $this;
    }
    
    /**
     * Adds a photo to vCard.
     * 
     * @param mixed   photoArray  a single photo or multiple photos as array( array(value, type) )
     * @param array   type        types to given photo (optional)
     * @param string  group       groupname of photo (optional)
     * 
     * @return this                    
     */
    public function addPhoto($photoArray, $type = null, $group = null) {
        $photoArray = self::_transformSingleToArray($photoArray, $type);
        $this->_addValuesLoop('photo', $photoArray, $group);
        
        return $this;
    }         
    
    /**
     * Adds a language to vCard.
     * 
     * @param mixed   langArray a single language or multiple languages as array( array(value, type) )
     * @param array   type      types to given language (optional)
     * @param string  group     groupname of language (optional)
     * 
     * @return this                    
     */
    public function addLang($langArray, $type = null, $group = null) {
        $langArray = self::_transformSingleToArray($langArray, $type);
        $this->_addValuesLoop('lang', $langArray, $group);
        
        return $this;
    }
    
    /**
     * Adds an address to vCard.
     * 
     * @param array addressArray  address specified as array(pobox, extendedaddress, street, city, region, postalcode, country, type=array(), group=null)
     *                            may also be multiple addresses     
     * 
     * @return this                    
     */
    public function addAddress($addressArray) {
        if (isset($addressArray['type']) || !isset($addressArray[0])) {
            $addressCacheArray = array();
            $addressCacheArray[] = $addressArray;
            $addressArray = $addressCacheArray;
        }
        
        foreach ($addressArray as $value) {
            $typeCacheArray = array();
            
            if (!isset($value['type'])) {
                $value['type'][] = null;
            } elseif (!is_array($value['type'])) {
                $typeCacheArray[] = $value['type'];
                $value['type'] = $typeCacheArray;
            }

            $typeArray = array();
            
            foreach ($value['type'] as $addressType) {
                if (in_array($addressType, $this->_addressConstantArray)) {
                    $typeArray[] = $addressType;
                }
            }
            
            $group = null;
            
            if (isset($value['group'])) {
                $group = $value['group'];
            }

            $this->_address[] = array('pobox' => (isset($value['pobox']) ? $value['pobox'] : ''),
                                      'extendedaddress' => (isset($value['extendedaddress']) ? $value['extendedaddress'] : ''),
                                      'street' => (isset($value['street']) ? $value['street'] : ''),
                                      'city' => (isset($value['city']) ? $value['city'] : ''),
                                      'region' => (isset($value['region']) ? $value['region'] : ''),
                                      'postalcode' => (isset($value['postalcode']) ? $value['postalcode'] : ''),
                                      'country' => (isset($value['country']) ? $value['country'] : ''),
                                      'type' => $typeArray,
                                      'group' => $group
                                     );
        }
        
        return $this;
    }
    
    /**
     * Adds a phonenumber to vCard.
     * 
     * @param mixed   phonenumberArray  a single phonenumber or multiple phonenumbers as array( array(value, type) )
     * @param array   type              types to given phonenumber
     * @param string  group             groupname of phonenumber
     * 
     * @return this                    
     */                   
    public function addPhonenumber($phonenumberArray, $type = null, $group = null) {
        if (!is_array($phonenumberArray)) {
            $phonenumberCacheArray = array(array('value' => $phonenumberArray));

            if (is_array($type)) {
                $phonenumberCacheArray[0]['type'] = $type;
            } else {
                $phonenumberCacheArray[0]['type'][] = $type;
            }
    
            $phonenumberArray = $phonenumberCacheArray;
        }
        
        $this->_addValuesLoop('telephone', $phonenumberArray, $group);
        
        return $this;
    }

    /**
     * Adds an email address to vCard.
     * 
     * @param mixed   emailArray  a single email address or multiple emailaddresses as array( array(value, type) )
     * @param array   type        types to given email address
     * @param string  group       groupname of email address
     * 
     * @return this                    
     */
    public function addEmail($emailArray, $type = null, $group = null) {
        $emailArray = self::_transformSingleToArray($emailArray, $type);
        $this->_addValuesLoop('email', $emailArray, $group);
        
        return $this;
    }
    
    /**
     * Adds an instant messenger to vCard.
     * 
     * @param mixed   imArray a single instant messenger or multiple instant messangers as array( array(value, messenger, type) )
     * @param array   type    types to given instant messenger (optional)
     * @param string  group   groupname of instant messenger (optional)
     * 
     * @return this                    
     */
    public function addInstantmessenger($imArray, $messenger = null, $type = null, $group = null) {
        if (!is_array($imArray) && is_string($imArray) && isset($messenger)) {
            $imCacheArray = array();
            $imCacheArray[0]['value'] = $imArray;
            $imCacheArray[0]['messenger'] = $messenger;
            
            if (is_array($type)) {
                $imCacheArray[0]['type'] = $type;
            } else {
                $imCacheArray[0]['type'][] = $type;
            }
            
            $imArray = $imCacheArray;
        }
    
        foreach ($imArray as $value) {
            $typeArray = array();
            
            foreach ($value['type'] as $imArray) {
                if (in_array($imArray, $this->_imConstantArray)) {
                    $typeArray[] = $imArray;
                }
            }
            
            if (in_array($value['messenger'], $this->_imMessengerConstantArray)) {
                $this->_im[] = array('value' => $value['value'], 'type' => $typeArray, 'messenger' => $value['messenger'], 'group' => $group);
            }
        }
        
        return $this;
    }
    
    /**
     * Adds a URL to vCard.
     * 
     * @param mixed   urlArray  a single URL or multiple URLs as array( array(value, type) )
     * @param array   type      types to given URL
     * @param string  group     groupname of URL
     * 
     * @return this                    
     */
    public function addUrl($urlArray, $type = null, $group = null) {
        $urlArray = self::_transformSingleToArray($urlArray, $type);
        $this->_addValuesLoop('url', $urlArray, $group);
        
        return $this;
    }
    
    /**
     * Adds a logo to vCard.
     * 
     * @param mixed   logoArray  a single logo or multiple logos as array( array(value, type) )
     * @param array   type       types to given logo (optional)
     * @param string  group      groupname of logo (optional)
     * 
     * @return this                    
     */
    public function addLogo($logoArray, $type = null, $group = null) {
        $logoArray = self::_transformSingleToArray($logoArray, $type);
        $this->_addValuesLoop('logo', $logoArray, $group);
        
        return $this;
    }
    
    /**
     * Adds a key to vCard.
     * 
     * @param mixed   keyArray  a single key or multiple keys as array( array(value, type) )
     * @param array   type      types to given key (optional)
     * @param string  group     groupname of key (optional)
     * 
     * @return this                    
     */
    public function addKey($keyArray, $type = null, $group = null) {
        $keyArray = self::_transformSingleToArray($keyArray, $type);
        $this->_addValuesLoop('key', $keyArray, $group);
        
        return $this;
    }
    
    /**
     * Adds a sound to vCard.
     * 
     * @param mixed   soundArray  a single sound or multiple sounds as array( array(value, type) )
     * @param array   type        types to given sound (optional)
     * @param string  group       groupname of sound (optional)
     * 
     * @return this                    
     */
    public function addSound($soundArray, $type = null, $group = null) {
        $soundArray = self::_transformSingleToArray($soundArray, $type);
        $this->_addValuesLoop('sound', $soundArray, $group);
        
        return $this;
    }
    
    /**
     * Adds a category to vCard.
     * 
     * @param mixed categoryArray a single category or multiple categories as array( array(value) )
     * 
     * @return this                    
     */
    public function addCategory($categoryArray) {
        $categoryArray = self::_transformSingleToArray($categoryArray, null);
        
        foreach ($categoryArray as $value) {            
            $this->_categories[] = array('value' => $value['value']);
        }
        
        return $this;
    }
    
    /**
     * Adds any other property to vCard.
     * 
     * @param string  key     property key 
     * @param string  value   property value
     * @apram array   types   to given property (optional)
     * @param string  group   groupname of given property (optional)
     * 
     * @return this                    
     */
    public function addOthers($key, $value, $type = null, $group = null) {    
        $this->_others[] = array('key' => $key,
                                 'value' => $value,
                                 'type' => $type,
                                 'group' => $group
                                );        
        return $this;
    }
    
}

?>