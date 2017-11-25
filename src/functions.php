<?php
/**
 * Created by PhpStorm.
 * User: farhan
 * Date: 25/11/17
 * Time: 4:00 PM
 */

/**
 * @param string $email
 * @param string $passkey
 * @return bool
 */

/*
 * TODO
 * Add Some security
 */
function login (string $email, string $passkey) {

    $user = Login::where('email',$email)->first();
    if(!$user)
        return false;
    if($passkey == $user['passkey'])
        return $user['id'];
    return false;
}

function getUser(int $id) {
    $user = Profile::find($id);
    if($user)
        return $user['name'];
    else
        return false;
}

function register(string $email, string $passkey,string $name) {
    $user = new Login();
    if(strlen($email)<=0 || strlen($passkey)<=0 || strlen($name)<=0)
        return false;
    $user->email = $email;
    $user->passkey = $passkey;
    $res = $user->save();
    if(!$res)
        return false;

    $profile = new Profile();
    $profile->id = $user->id;
    $profile->name = $name;
    $profile->save();
    return $user->id;
}