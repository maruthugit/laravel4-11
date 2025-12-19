<?php

$test = Config::get('constants.ENVIRONMENT');

if ($test == 'test')
{
return [
    'server' => '',
];
}
else
{
return [
    'server' => '',

];
// return ['server' => 'http://images.jocom.com.my',];
}
