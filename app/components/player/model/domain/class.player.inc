<?php 
class player extends PropertyObject
{
      function player($ar="",$col="",$DBE=null)
      {
            $this->table        ="player";
            $this->primaryKey   ="id_player";
            if($DBE!=null)
                $this->DBE = $DBE;
            else $this->DBE = MadHorse::getBone("DBE");
           
            if(!is_array($ar))
            { 
                  if($ar!="")
                  {
                        $ar = $this->getData($ar,$col);
                  }
                  else $ar = array();
            }
            $this->PropertyObject($ar);
      }
      
      function setPassword($val)
      {
            return $this->encryptPassword($this->data["email"],$val);
      }
      
      function encryptPassword($user,$pass)
      {
          $fixedSalt    =  MadHorse::getConfig("Security.salt");
          $variableSalt =  explode("@",$user)[0] ;
          $passString   = $fixedSalt.$pass.$variableSalt;          
          return sha1($passString);
      }
      
      function Add($params,$exceptvalue="action")
      {
          $params["hash"]="p".date(U);
          $res = parent::Add($params,$exceptvalue);
          
          if($res)
                $this->sendVerificationEmail();  
                
          return $res;
      }
      
      function sendVerificationEmail()
      {
          $link = URL::getBaseUrL()."player/verify/hash/".$this->get("hash");
          $emailBody = "<p> Please click on the link below to verify your email address <br /> <a href='$link'>$link</a></p>";

          $objMailer = new Mailer();
          $objMailer->MailTo($this->get("email"));
          $objMailer->From(MadHorse::getConfig("site_setting.default.mail_from"));
          $objMailer->Subject("Email Verification");
          $objMailer->Title("Verify you email address.");
          $objMailer->Body($emailBody);
          $objMailer->SendMail();          
      }
      
      function verify()
      {
          $this->set("email_verified",1);
          $this->set("hash","NULL");
          return $this->save();
      }
      
      function authenticate($data,$type=1)
      {
          $username= $data["email"];
          $password= $data["password"];
          $this->DBE->simpleSelect('*',$this->table,"email='".
                              mysqli_real_escape_string($this->DBE->getConId(),$username)
                              ."' and password='".
                              $this->encryptPassword($username,$password)
                              ."' and status=1") ; 
          $ar = $this->DBE->fetchRow();
          $this->PropertyObject($ar);
          
          if($this->DBE->getNumRows() )
          {
              $this->_updateUserSession($ar);
              $this->_saveAuthentication($ar);
              return TRUE;
          }
          return FALSE;
      }
      
      function _updateUserSession($ar)
      {
          $this->Session->unsetVar("player");
          $this->Session->setVar("player",array(
                                            "name"=>$ar["last_name"].", ".$ar["first_name"],
                                            "id"=>$ar['id_player'],
                                            "login"=>"1",
                                            "last_login"=>$ar["last_login"]
                                          )
                                 );
          
      }
      function _saveAuthentication($ar)
      {
          $this->set("last_login",date("Y-m-d H:i:s"));
          $this->save();
      }
      
      function getShortName()
      {
          return $this->get("last_name").", ".substr($this->get("first_name"),0,1);
      }
      
      function getLeaderBoardStat()
      {
          if($this->get("lb_position") =="")
          {
                $this->DBE->simpleSelect('x.position, x.point', 
                                   '(SELECT t.`id_player`,
                                       t.`point`,
                                       @rownum := @rownum + 1 AS position
                                   FROM leader_board t
                                   JOIN (SELECT @rownum := 0) r
                                   WHERE t.site = '.$this->get("site").'
                                   ORDER BY t.`point` desc) x',
                                  "id_player=".$this->get($this->primaryKey));
                $ar = $this->DBE->fetchRow();
                $this->set("lb_position",$ar["position"]);
                $this->set("point",$ar["point"]);
          }
          return array("position"=>$this->get("lb_position"), "point"=>$this->get("point"));
      }
      function verifyResetPasswordLink($hash)
      {
          $hash = $hash/$this->get($this->primaryKey);
          return ($hash == $this->get("hash") && ($hash + 24*3600) >= date("u") ) ;
      }
      function forgetPassword()
      {
          $hash = date("U");
          $this->set("hash",$hash);
          $this->save();
          
          $hash *= $this->get($this->primaryKey);
          $link = URL::getBaseUrL()."player/resetPassword/hash/".$hash."/email/".$this->get("email");
          $emailBody = "<p> Please click on the link below to reset your Password <br /> <a href='$link'>$link</a></p> <br /> <br /> This link is valid for 24 hours.";

          $objMailer = new Mailer();
          $objMailer->MailTo($this->get("email"));
          $objMailer->From(MadHorse::getConfig("site_setting.default.mail_from"));
          $objMailer->Subject("Password Reset link");
          $objMailer->Title("Forget Password");
          $objMailer->Body($emailBody);
          $objMailer->SendMail();          
      }
      
      /**
      * Load user information from FB
      * Check the userinformation for existing user. 
      *     If the users already exists, load user id
      *        Update user information in the database
      *     If the user do not exists
      *         Save the user if $saveNewUser is true
      * 
      * @param mixed $objFBApp
      * @param mixed $redirectURL
      * @param mixed $saveNewUser (bool)
      * 
      * @return 
      *     FB_USER_EXISTS          if the user already exists in the database
      *     FB_USER_ADDED_SUCCESS   if the usder do not exists but added successfully
      *     FB_USER_ADDED_FAIL      if the usder do not exists and added also failed or saveNewUser is false
      *     FB_USER_SYSTEM_ERROR    for any other error
      */
      function loadFromFB($objFBApp,$redirectURL,$saveNewUser = true)
      {
          $accessTokenRes = $objFBApp->getAccessToken($redirectURL);
          
          if($accessTokenRes["success"])
          {
              $fields       = ["first_name","last_name"];
              $userDataRes  = $objFBApp->getUserDetail($fields);
              
              if($userDataRes["success"])
              {
                  $userData = $userDataRes["result"];

                  $email    = $this->_createFBUserEmail($userData["user_id"]);
                  $this->set("email"     ,$email);
                  $this->set("first_name",$userData["first_name"]);
                  $this->set("last_name" ,$userData["last_name" ]);
                  
                  $ar = $this->getData($email,"email");
                  if($ar!= null && $ar != false)
                  { 
                      // user exists
                      $this->set("id_player",$ar["id_player"]);
                      $this->_updateUserSession($ar);
                      $this->_saveAuthentication($ar);
                      return FB_USER_EXISTS;
                  }
                  else if($this->DBE->result!=false)
                  {
                      $this->set("last_login",date("Y-m-d H:i:s"));
                      $this->save();
                      $this->_updateUserSession($this->data);
                      return FB_USER_ADDED_SUCCESS;
                  }
                  else return FB_USER_ADDED_FAIL;
              }
              else  return FB_USER_SYSTEM_ERROR;
          }
          else  return FB_USER_SYSTEM_ERROR;
      }
      function _createFBUserEmail($fbUserId)
      {
          return "dg{$fbUserId}@fb.com";
          
      }
}