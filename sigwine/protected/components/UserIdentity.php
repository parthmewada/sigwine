<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    private $_id;

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {
        
        $user = UserMaster::model()->findByAttributes(array('user_name' => $this->username));
        if ($user === null) { // No user found!
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if ($user->user_pass !== base64_encode($this->password)) { // Invalid password!
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            /**
             * @desc: Start the Session Here
             */
            $session = new CHttpSession;
            $session->open();
            /**
             * @desc: User Full Name
             */
            $session["fullname"] = ucfirst($user->first_name);
            /**
             * @desc: User ID
             */
            $session["uid"] = $user->u_id;
           /**
            * @desc: User Role Id for ACL Management
            */
            $session["rid"] = $user->ur_id;
            /**
             * @desc : User name
             */
            $session["uname"] = $user->first_name;
            $this->setState('id', $user->ur_id);
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }
    public function getId() {       //  override Id
        return $this->_id;
    }

}