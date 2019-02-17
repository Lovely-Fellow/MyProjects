<?php
/**
 * Created by PhpStorm.
 * User: vidhi_BSP
 * Date: 10/31/2018
 * Time: 11:30 AM
 */
session_start();

//$url ='https://suite.social/coder/call/bsp/';
$url ='http://train.social.com/';

/*
 * Admin Login.
*/


if(isset($_POST['action']) && $_POST['action']=='login_admin'){
    $data =array();
    if(isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['password']) && !empty($_POST['password'])){
        if($_POST['username'] =='admin' && $_POST['password'] ='admin'){
            $data['status'] = 'TRUE';
            $_SESSION['username'] = $_POST['username'];
        }else{
            $data['status'] = 'FALSE';
        }

    }else{
        $data['status'] = 'FALSE';
    }
    header('Content-Type: application/json');
    echo json_encode($data);
}

/* Logout Admin. */

if(isset($_POST['action']) && $_POST['action']=='logout_admin'){

    unset($_SESSION["username"]);
    header('Content-Type: application/json');
    $data['status'] = 'TRUE';
    echo json_encode($data);
}

/* Check Contact Is already exist or not.
*/
if(isset($_POST['action']) && $_POST['action']=='check_contact'){
    $data['status'] = 'False';
    $current_data = file_get_contents('data.json');
    $array_data = json_decode($current_data, true);
    foreach ($array_data as  $value) {

        if ($value['phone'] == $_POST['phone']) {
            $data['status'] = 'TRUE';
        }
    }
    header('Content-Type: application/json');

    echo json_encode($data);
}

/* Add new Contact in json.*/

if(isset($_POST['action']) && $_POST['action']=='add_new_contact'){
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
         $file_name = basename( $_FILES["image"]["name"]);
    }
    if(file_exists('data.json'))
    {
        $current_data = file_get_contents('data.json');

        $id = 0;

        if(isset($current_data)){

            $array_data = json_decode($current_data, true);
            foreach ($array_data as  $value) {

                if ($value['phone'] == $_POST['phone']) {
                    header("Location: {$_SERVER['HTTP_REFERER']}");
                    exit;
                }
            }
            $last_array= end($array_data);         // move the internal pointer to the end of the array
            $id = $last_array['id'] + 1;
        }

        if(isset($file_name) && !empty($file_name)){
            $file_name =$file_name;
        }else{
            $file_name = 'user_default.png';
        }
        $extra = array(
            'id'  => $id,
            'name' => $_POST['name'],
            'phone' => $_POST["phone"],
            'url'  =>  $_POST["url"],
            'photo' => $url.'uploads/'.$file_name
        );
        $array_data[] = $extra;
        $final_data = json_encode($array_data);
        file_put_contents('data.json', $final_data);

    }
    else
    {
        $error = 'JSON File not exits';
    }

    header("Location: ".$url."admin.php");
}

/* Delete contact from Contact List (Json File)*/

if(isset($_POST['action']) && $_POST['action']=='delete_contact'){
    $current_data = file_get_contents('data.json');
    $array_data = json_decode($current_data, true);

    foreach ($array_data as $key => $value) {
        if ($value['id'] == $_POST['id']) {
            unset($array_data[$key]);
            break;
        }
    }
    $json_arr = array_values($array_data);
    $final_data = json_encode($json_arr);
    file_put_contents('data.json', $final_data);
    header('Content-Type: application/json');
    $data['status'] = 'TRUE';
    echo json_encode($data);
}
