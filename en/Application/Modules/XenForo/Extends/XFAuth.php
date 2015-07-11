<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mr.UBKey
 * Date: 1/8/13
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */
#[Adapter('guitar_xf')]
#[Table('user_authenticate')]
#[PrimaryKey('user_id')]
class XFAuth extends XPHP_Model
{
    /**
     * Default Salt Length
     *
     * @var integer
     */
    const DEFAULT_SALT_LENGTH = 10;

    public $user_id;

    public $scheme_class = "XenForo_Authentication_Core";

    /**
     * Password info for this authentication object
     *
     * @var array
     */
    public $data;

    public $remember_key;

    public $password;

    /**
     * Hash function to use for generating salts and passwords
     *
     * @var string
     */
    protected $_hashFunc = '';

    public function _init()
    {
        $this->data = unserialize($this->data);
        $this->_hashFunc = $this->data['hashFunc'];
    }

    /**
     * Setup the hash function
     */
    protected function _setupHash()
    {
        if ($this->_hashFunc)
        {
            return;
        }

        if (extension_loaded('hash'))
        {
            $this->_hashFunc = 'sha256';
        }
        else
        {
            $this->_hashFunc = 'sha1';
        }
    }

    /**
     * Perform the hashing based on the function set
     *
     * @param string
     *
     * @return string The new hashed string
     */
    protected function _createHash($data)
    {
        $this->_setupHash();
        switch ($this->_hashFunc)
        {
            case 'sha256':
                return hash('sha256', $data);
            case 'sha1':
                return sha1($data);
            default:
                throw new XPHP_Exception("Unknown hash type");
        }
    }

    protected function _newPassword($password, $salt)
    {
        $hash = $this->_createHash($this->_createHash($password) . $salt);
        return array('hash' => $hash, 'salt' => $salt, 'hashFunc' => $this->_hashFunc);
    }

    /**
     * Generate new authentication data
     * @see XenForo_Authentication_Abstract::generate()
     */
    public function generate($password)
    {
        if (!is_string($password) || $password === '')
        {
            return false;
        }

        $salt = $this->_createHash(self::generateSalt());
        $data = $this->_newPassword($password, $salt);
        return serialize($data);
    }

    /**
     * Authenticate against the given password
     * @see XenForo_Authentication_Abstract::authenticate()
     */
    public function authenticate($userId, $password)
    {
        if (!is_string($password) || $password === '' || empty($this->data))
        {
            return false;
        }
        $userHash = $this->_createHash($this->_createHash($password) . $this->data['salt']);
        return ($userHash === $this->data['hash']);
    }

    /**
     * Generates an arbtirary length salt
     *
     * @return string
     */
    public static function generateSalt($length = null)
    {
        if (!$length)
        {
            $length = self::DEFAULT_SALT_LENGTH;
        }

        return XPHP_String::randomString($length);
    }

    public function insert()
    {
        $arr = array();
        $arr['user_id'] = $this->user_id;
        $arr['scheme_class'] = $this->scheme_class;
        $salt = self::generateSalt();
        $hash = $this->_createHash($this->_createHash($this->password) . $salt);
        $arr['data'] = $this->generate($this->password);
        return $this->db->insert($arr);
    }
}
