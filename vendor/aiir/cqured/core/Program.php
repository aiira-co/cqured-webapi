<?php
/**
 * Programe Class
 */
namespace Cqured\Core;

use Cqured\Router\ActivatedRoute;
use Cqured\Router\Node;
use Cqured\Core\Component;
use Cqured\Core\Legacy;
use Cqured\Core\Render;
use Cqured\Router\Routes;

/**
 * Program Class exists in the Cqured\Core namespace
 * This class is the framework itself. It handles error reporting, instancing, and configuring
 *
 * @category Core
 */
class Program
{
    // private $bootstrap;
    public static $bootstrap;
    private static $errorReport;
    private static $_zenoConfig;
    private static $instance = [];

    public function __construct($config)
    {
        self::$_zenoConfig = $config;
        $this->cors($config->allow_origin, $config->allow_methods, $config->max_age);
    }

    /**
     * This Method is called in the root index.php file.
     * The Method intanciates the node class for routing.
     *
     * @return null
     */
    public function route()
    {
        $node = new Node();
        $node->router(self::$_zenoConfig);
        // $this->aleph = $node->aleph;
    }







    /**
     * Method to Instantiate a class.
     * It first checks to see if class is available
     * if yes, return the instance of the class
     *   hence [new className]
     * if class is not available, require the class file, then repeat the
     * process again to instantiate
     */
    public static function getInstance($class)
    {
        // check if instance of the class already exist
        if (isset(self::$instance[$class])) {
            return self::$instance[$class];
        } else {
            // check if class is available
            if (!class_exists($class)) {
                self::autoload($class, 'core');
            }

            if ($class === 'Legacy') {
                self::$instance[$class] = new Legacy;
            } elseif ($class === 'Render') {
                self::$instance[$class] = new Render;
            } elseif ($class === 'Routes') {
                self::$instance[$class] = new Routes;
            } else {

              // $formatClass = ucfirst($class)
                self::$instance[$class] = new $class;
            }


            return self::$instance[$class];
        }
    }






    /**
     * Automatically load required for to instatiate the class
     */
    public static function autoload(string $class, string $instanceType=''): bool
    {
        // echo memory_get_usage();

        $path=[];

        // Reduce target directories to query
        switch ($instanceType) {
          case 'core':
            $paths = [
              '.'.DS.'core',
              '.' //for airjax
            ];
            break;

          case 'component':
          // echo'<pre>component loading...';
            $paths = [
              '.'.DS.'',
              '.'.DS.self::$bootstrap.DS.'components',
              '..'.DS.self::$bootstrap.DS.'components'
            ];
            break;

          default:
          $paths = [
            '.',
            'core',

            '.'.DS.self::$bootstrap,
            '.'.DS.self::$bootstrap.DS.'models',
            '..'.DS.self::$bootstrap.DS.'components',
            '..'.DS.self::$bootstrap.DS.'models'];
            break;
        }


        foreach ($paths as $path) {
            $file = $path.DS.strtolower($class).'.php';

            // echo $file.'<br/>';

            if (file_exists($file)) {
                // echo'found';
                include_once $file;
                return true;
            }
        }



        return false;
    }


    /**
     * Method to report error & kill app process
     */
    public static function reportError(string $error, string $errorTitle='Error Report')
    {
        if (!self::$_zenoConfig->enableProdMode) {
            self::$errorReport = json_encode(['error'=>$errorTitle, 'message' => $error]);
        } else {
            self::$errorReport = '';
        }

        die(self::$errorReport);
    }






    /**
     * Renders
     *
     * This method takes the view and component obj for rendering.
     * it sets them for the CoreApp Method which is ad only declared in the
     *  workspace template where the component will be seen in the UI of the app or
     * website.
     */
    public static function render()
    {
        $renderOutput =self::getInstance('Render');
        $params = new ActivatedRoute;

        // instead of params, use the header's application to see the return
        //also check for password if it matches.

        if (self::$_zenoConfig->content_type === 'xml') {
            // $xml = new SimpleXMLElement('<data/>');
            // array_walk_recursive(json_decode(json_encode($basket->result),true),[$xml,'addChild']);

            // echo $result;
            // self::arrayToXml($basket->result,$xml);
            // self::arrayToXml(['name'=>'ama'],$xml);

            // array_walk_recursive($result,[$xml,'addChild']);
            // print_r($basket->result);
            // echo $xml->asXML();
            echo xmlrpc_encode($renderOutput->result);
        } else {
            echo json_encode($renderOutput->result);
        }
    }





    /**
     *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
     *  origin.
     *
     *  In a production environment, you probably want to be more restrictive, but this gives you
     *  the general idea of what is involved.  For the nitty-gritty low-down, read:
     *
     *  - https://developer.mozilla.org/en/HTTP_access_control
     *  - http://www.w3.org/TR/cors/
     *
     */
    public function cors($origin, $methods, $age)
    {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:

            $origin == '*' ? header("Access-Control-Allow-Origin:{$_SERVER['HTTP_ORIGIN']}") : header("Access-Control-Allow-Origin:".$origin) ;
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age:'.$age);    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods:".$methods);
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(0);
        }
    }


    /**
     * Method for redirecting.
     * This method is also used in the node class for redirecting routes
     */
    public static function redirect($url, $redirectTo = false, $code = 302)
    {
        if ($redirectTo) {
            $airJaxURL = '&api=airJax';
            $url = $adConfig->airJax ? $url.$redirectTo.$airJaxURL : $url.$redirectTo;
        } else {
            $airJaxURL = '?api=airJax';
            $url = $adConfig->airJax ? $url.$airJaxURL : $url;
        }


        if (strncmp('cli', PHP_SAPI, 3) !== 0) {
            if (!headers_sent()) {
                if (strlen(session_id()) > 0) { // if using sessions
                    session_regenerate_id(true); // avoids session fixation attacks
                    session_write_close(); // avoids having sessions lock other requests
                }

                if (strncmp('cgi', PHP_SAPI, 3) === 0) {
                    header(sprintf('Status: %03u', $code), true, $code);
                }

                header('Location: ' . $url, true, (preg_match('~^30[1237]$~', $code) > 0) ? $code : 302);
            } else {
                echo "<meta http-equiv=\"refresh\" content=\"0;url=$url\">\r\n";
            }

            exit();
        }
    }
}
