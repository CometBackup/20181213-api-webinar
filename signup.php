<?php

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $c = new \Comet\Server("http://127.0.0.1:9960/", "admin", "admin");

    try {
        // Create the account
        $c->AdminAddUser($username, $password);

        // Modify the account properties
        $profile = $c->AdminGetUserProfile($username);
        // $profile->IsSuspended = true;
        $profile->PolicyID = "fdefabe4-7b16-4f42-9fd5-c04b8b2fab8f";
        $c->AdminSetUserProfile($username, $profile);

        // mail("accounts@my-company.example.com", "New signup!", "Customer {$username} just signed up!");

        die("Your account was created! Now <a href>download the client!</a>");

    } catch (\Exception $e) {

        die("Couldn't create account: " . $e->getMessage());

    }
    
}

?>
<!DOCTYPE html>

<form method="POST">
    <input type="text" placeholder="username" name="username">
    <input type="password" placeholder="password" name="password">
    <input type="submit" value="Sign up!">
</form>
