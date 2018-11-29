<?php
use Cqured\Core\Program;

$apiRoutes = [
  [
    'path'=>'values',
    'controller'=>'ValuesController'
  ],
  [
    'path'=>'person',
    'controller'=>'PersonController',
    'authguard'=> ['AuthenticationModel']
  ]
];


$apiRouterModule = Program::getInstance('Routes');
$apiRouterModule->setRouter($apiRoutes);
