<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mr.UBKey
 * Date: 1/8/13
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */
#[Adapter('guitar_xf')]
#[Table('user')]
#[PrimaryKey('user_id')]
class XFUser extends XPHP_Model
{
    #[Join(table = 'user_field_value')]
    public $user_id;

    public $username;

    public $email;

    public $gender = 'male';

    public $custom_title = "";

    public $language_id = 1;

    public $style_id = 0;

    public $timezone = 'Asia/Bangkok';

    public $visible = 1;

    public $user_group_id = 2;

    public $secondary_group_ids = "";

    public $display_style_group_id = 2;

    public $permission_combination_id = 2;

    public $message_count = 0;

    public $conversations_unread = 0;

    public $register_date;

    public $last_activity;

    public $trophy_points = 0;

    public $alerts_unread = 0;

    public $avatar_date = 0;

    public $avatar_width = 0;

    public $avatar_height = 0;

    public $gravatar = "";

    public $user_state = 'email_confirm';

    public $is_moderator = '';

    public $is_admin = 0;

    public $is_banned = 0;

    public $like_count = 0;

    public $warning_points = 0;

    protected $_user;

    /**
     * Get user info
     * @return mixed
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Logs the given user in (as the visiting user). Exceptions are thrown on errors.
     *
     * @param string $nameOrEmail User name or email address
     * @param string $password
     * @param string $error Error string (by ref)
     *
     * @return integer|false User ID auth'd as; false on failure
     */
    public function validateAuthentication($nameOrEmail, $password, &$error = '')
    {
        $user = $this->getUserByNameOrEmail($nameOrEmail);
        if (!$user)
        {
            $error = "Không tìm thấy tài khoản";
            return false;
        }

        $auth = new XFAuth($user->user_id);

        if (!$auth->authenticate($user->user_id, $password))
        {
            $error = "Mật khẩu không chính xác";
            return false;
        }

        $this->_user = $user;

        return $user->user_id;
    }

    /**
     * Returns a user record based on an input username OR email
     *
     * @param string $input
     * @param array $fetchOptions User fetch options
     *
     * @return array|false
     */
    public function getUserByNameOrEmail($input, array $fetchOptions = array())
    {
        if ($this->couldBeEmail($input))
        {
            if ($user = $this->getUserByEmail($input, $fetchOptions))
            {
                return $user;
            }
        }

        return $this->getUserByName($input, $fetchOptions);
    }

    /**
     * Checks to see if the input string *might* be an email address - contains '@' after its first character
     *
     * @param String $email
     *
     * @return boolean
     */
    public function couldBeEmail($email)
    {
        if (strlen($email) < 1)
        {
            return false;
        }

        return (strpos($email, '@', 1) !== false);
    }

    /**
     * Returns a user record based on an input email
     *
     * @param string $email
     * @param array $fetchOptions User fetch options
     *
     * @return array|false
     */
    public function getUserByEmail($email)
    {
        $result = $this->db->where('user.email', $email)
                           ->get()
                           ->result();
        return isset($result[0]) ? $result[0] : false;
    }

    /**
     * Returns a user record based on an input username
     *
     * @param string $username
     * @param array $fetchOptions User fetch options
     *
     * @return array|false
     */
    public function getUserByName($username)
    {
        $result = $this->db->where('user.username', $username)
                           ->get()
                           ->result();
        return isset($result[0]) ? $result[0] : false;
    }
    
    public function getUsersByGroup($group_id)
    {
        $result = $this->db->where('user_group_id', $group_id)
                           ->get()
                           ->result();
        return $result;
    }

    public function insert()
    {
        $user_id = parent::insert();

        //Insert user_profile
        $arrUI = array();
        $arrUI['user_id'] = $user_id;
        $arrUI['dob_day'] = 0;
        $arrUI['dob_month'] = 0;
        $arrUI['dob_year'] = 0;
        $arrUI['status'] = "";
        $arrUI['status_date'] = 0;
        $arrUI['status_profile_post_id'] = 0;
        $arrUI['signature'] = "";
        $arrUI['homepage'] = "";
        $arrUI['location'] = "";
        $arrUI['occupation'] = "";
        $arrUI['following'] = "";
        $arrUI['ignored'] = "";
        $arrUI['csrf_token'] = "e57f6bb3bbaa60f14ab9ad2d145ad88aa4ff2b1d";
        $arrUI['avatar_crop_x'] = 0;
        $arrUI['avatar_crop_y'] = 0;
        $arrUI['about'] = "";
        $arrUI['facebook_auth_id'] = 0;
        $arrUI['custom_fields'] = serialize(array());
        $this->db->insert($arrUI, 'user_profile');

        //Insert user_privacy
        $arrUP = array();
        $arrUP['user_id'] = $user_id;
        $arrUP['allow_view_profile'] = "everyone";
        $arrUP['allow_post_profile'] = "members";
        $arrUP['allow_send_personal_conversation'] = "members";
        $arrUP['allow_view_identities'] = "everyone";
        $arrUP['allow_receive_news_feed'] = "everyone";
        $this->db->insert($arrUP, 'user_privacy');

        //Insert user_option
        $arrUO = array();
        $arrUO['user_id'] = $user_id;
        $arrUO['show_dob_year'] = 1;
        $arrUO['show_dob_date'] = 1;
        $arrUO['content_show_signature'] = 1;
        $arrUO['receive_admin_email'] = 1;
        $arrUO['email_on_conversation'] = 1;
        $arrUO['is_discouraged'] = 0;
        $arrUO['default_watch_state'] = 'watch_email';
        $arrUO['alert_optout'] = '';
        $arrUO['enable_rte'] = 1;
        $this->db->insert($arrUO, 'user_option');

        return $user_id;
    }
}
