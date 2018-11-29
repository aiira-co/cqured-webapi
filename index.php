<?php

/**
 * Copyright (c) 2018 ProjectAIIR.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     [ ProjectAIIR ]
 * @subpackage  [ cqured ]
 * @author      Owusu-Afriyie Kofi <koathecedi@gmail.com>
 * @copyright   2018 ProjectAIIR.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://airDesign.co.nf
 * @version     @@2.00@@
 */

 declare(strict_types=1);

 use Cqured\Core\Program;

 session_start();

 header('Content-Type:application/json;  charset=utf-8');

 /**
  * Boostrap the app
  */
 class Startup
 {
     private $zenoConfig;

     // the bootraped will tell us the router


     //check if the app is offline,
     //if yes, show offline page and account login,
     //if login, display app

     //else if no, show the default page that is set as home.
     //check if the page exists,
     //if yes, show page
     //else, it show error page

     public function __construct()
     {
         require_once 'config.php';

         $this->zenoConfign = new Config;

         if ($this->zenoConfign->offline) {
             echo json_encode(["noti"=>"success","result"=>$zenoConfig->offline_message]);
         } else {
             define('DS', DIRECTORY_SEPARATOR);
             require_once __DIR__.DS.'vendor'.DS.'autoload.php';
             $this->bootstrapApp() ;
         }
     }



     private function bootstrapApp()
     {
         // print_r(new Config);
         $api = new Program(new Config);

         $api->route(); 
     }
 }

 $app = new Startup;
