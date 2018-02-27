<?php 
class player1 extends entity
{
    const HASH_ALGORITHM = PASSWORD_BCRYPT;
    const HASH_COST = 12;
     
    function __construct($ar)
    {
        parent::__construct($ar);
    }
    
    public function getFingerPrint()
    {
        return $this->id_player;
    }
    
    protected function setEmailAddress(string $email)
    {
        $email = strtolower($email);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new MadhorseException($email." is not a valid email");
        return $email;
    }
    
    protected function  setPassword(string $password)
    {
        $passString   = $this->_createPasswordString($password);
        return password_hash($passString,self::HASH_ALGORITHM,['cost'=> SELF::HASH_COST]);
    }
    
    public function matchPassword(string $password): bool
    {
        $passString   = $this->_createPasswordString($password);
        $passHash     = $this->password;
        return password_verify($passString,$passHash);
                
    }
    
    private function _createPasswordString($password): string
    {
        $email        = $this->email;
        $fixedSalt    =  MadHorse::getConfig("Security.salt"); // anti pattern. Secretely getting input from  outside
        $variableSalt =  explode("@",$email)[0] ;
        $passString   = $fixedSalt.$password.$variableSalt;
        
        return $passString;
    }
}