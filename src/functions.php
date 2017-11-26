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

function nearby($latitude,$longitude) {
    $latitude = (float)$latitude;
    $longitude = (float)$longitude;
    $centres = Centre::whereBetween('loc_latt',[$latitude-100.025,$latitude+100.025])
                        ->whereBetween('loc_long',[$longitude-100.025,$longitude+100.025])
                        ->get();

    //$centres = Centre::all();
    return $centres;
}

// Validations
function validate_centre($cred) {
    return true;
}

function validate_user($userid,$centreid,$date,$token) {
    $today = date('d-m-Y');
    if($date!=$today)
        return false;
    $res = Booking::where('userid',$userid)
                    ->where('centreid',$centreid)
                    ->get();
    if($res){
        $checkin = new Checkin();
        $checkin->userid = $userid;
        $checkin->centreid = $centreid;
        if($checkin->save())
            return true;
    }
    return false;

}