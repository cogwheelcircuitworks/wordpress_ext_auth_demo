<?php

/*

ext_auth_demo_server.php 

This would be replaced by a call to the OAuth Server

*/

if($_GET['user'] == 'bob' && $_GET['pass'] == 'asdf') {
  echo json_encode(array('result' => '1', 'username' => 'bob', 'first_name' => 'Bob', 'last_name' => 'Jacobsen', 'email' => 'aaben@lobaugh.net'));
} else {
  echo json_encode(array('result' => '0'));
}
