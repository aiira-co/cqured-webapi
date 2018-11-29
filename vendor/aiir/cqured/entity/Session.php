<?php
namespace Cqured\Entity;

/**
 * Session Class exists in the Cqured\Entity namespace
 * This class is for authentication
 *
 * @category Entity
 */
class Session
{

// ==================================================================
//
    // User Login
//
    // ------------------------------------------------------------------
    private static $user_id;
    private static $table;
    private static $emailField;
    private static $usernameField;
    private static $hashField;

    public static $error;

    private $entityModel;


    // Method for initializing Session
    public function __construct($table = 'user', $emailField = 'email', $usernameField = 'username', $hashField = 'hashword')
    {
        self::$table = $table;
        self::$emailField = $emailField;
        self::$usernameField = $usernameField;
        self::$hashField = $hashField;
    }

    // Method to Login User
    public static function SessionLogin(string $uname, string $umail, string $upass):bool
    {
        try {
            $pdo = CORE::getInstance('pdo');

            if (self::$table =='') {
                self::$SessionInit();
            }

            require_once 'config.php';
            $prefix  = (new Config)->dbprefix;
            $sql = 'SELECT * FROM '.$prefix.self::$table.' WHERE ('.self::$usernameField.'=:uname || '.self::$emailField.'=:umail)  LIMIT 1';
            // echo $sql;
            $query = $pdo->prepare($sql);
            $query->execute([':uname'=>$uname, ':umail'=>$umail]);
            $userRow=$query->fetch(5);


            if ($query->rowCount() > 0) {
                // check if account is active
                // echo $userRow->acount_enabled;
                if ($userRow->account_enabled) {
                    // check is the accout is on lockout
                    if ($userRow->lockout_enabled) {
                        echo 'current time is '.strtotime(date('Y-m-d h:i:s')).' <br>time left is:: '.(strtotime(date('Y-m-d h:i:s')) - $userRow->lockout_end);
                        if ((strtotime(date('Y-m-d h:i:s')) - $userRow->lockout_end) > 0) {
                            echo '<br> changing lock here --> '. $userRow->lockout_enabled.'<br/>';
                            $this->entityModel->table(self::$table)
                            ->where('id', $userRow->id)
                            ->update(
                              [
                                'lockout_enabled' => false,
                                'lockout_end'=> 0
                              ]
                                  );

                            self::$error = '';
                            return false;
                        } else {
                            self::$error = 'locked';
                            return false;
                        }
                    } else {
                        // verify password
                        if (password_verify($upass, $userRow->{self::$hashField})) {
                            $this->entityModel->table(self::$table)
                            ->where('id', $userRow->id)
                            ->update(
                              [
                                'access_failed_count'=>0,
                                'lockout_enabled' => false ,
                                'lockout_end'=> 0
                              ]
                                  );

                            $_SESSION['user_session'] = $userRow->id;
                            $_SESSION['count'] = 0;
                            self::$user_id = $userRow->id;
                            return true;
                        } else {
                            self::$error = 'passwordError';
                            echo 'failed:: '.$userRow->access_failed_count;
                            if ($userRow->access_failed_count < 5) {
                                $this->entityModel->table(self::$table)
                                ->where('id', $userRow->id)
                                ->update(array('access_failed_count'=>$userRow->access_failed_count + 1));
                            } elseif ($userRow->access_failed_count == 5) {
                                $this->entityModel->table(self::$table)
                                ->where('id', $userRow->id)
                                ->update(
                                [
                                 'access_failed_count'=>$userRow->access_failed_count + 1,
                                 'lockout_enabled' => true ,
                                 'lockout_end'=> strtotime(date('Y-m-d h:i:s')) + 300
                                ]
                                     );
                            } elseif ($userRow->access_failed_count < 10) {
                                $this->entityModel->table(self::$table)
                                ->where('id', $userRow->id)
                                ->update(['access_failed_count'=>$userRow->access_failed_count + 1]);
                            } elseif ($userRow->access_failed_count == 10) {
                                $this->entityModel->table(self::$table)
                                ->where('id', $userRow->id)
                                ->update(
                                [
                                  'account_enabled' => false,
                                  'access_failed_count'=>$userRow->access_failed_count + 1,
                                  'lockout_enabled' => true ,
                                  'lockout_end'=> strtotime(date('Y-m-d h:i:s')) + 86700
                                ]
                                     );
                            }
                            return false;
                        }
                    }
                } else {
                    self::$error = 'notActive';
                    return false;
                }
            } else {
                self::$error = 'notExist';
                return false;
            }
        } catch (PDOException $e) {
            echo 'its false';
            echo $e->getMessage();
        }
    }



    // ==================================================================
//
    // Check If User is logged in
//
    // ------------------------------------------------------------------

    public static function IsLoggedIn():bool
    {
        if (isset($_SESSION['user_session'])) {
            return true;
        } else {
            return false;
        }
    }




    public function SessionMessage(string $msg = "")
    {
        if ($_SESSION['message'] =="") {
            $_SESSION['message'] = $msg;
        } else {
            return $_SESSION['message'];
        }
    }



    // ==================================================================
//
    // Logs User Out
//
    // ------------------------------------------------------------------



    public function SessionLogout(): bool
    {
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }
}
