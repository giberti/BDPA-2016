<?php

require __DIR__ . '/../setup.php';

// Get our data from the request
$method   = isset($_POST['method']) ? $_POST['method'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$username = ctype_alnum($_POST['username']);

// An associative array to hold information the frontend might need
$responseData = array();

// Decide what to do based on the value pass in the 'method'
switch ($method) {
    case 'addUser': // registers the provided username

        if (addUser($username, $password)) {
            // Send a 201 (created) to indicate the creation was successful
            http_response_code(201);
        } else {
            // send a 409 (conflict) to indicate there was a problem (usually a race condition on the username)
            http_response_code(409);
        }
        break;

    case 'isAvailable': // checks to see if the username is available

        if (isAvailable($username)) {
            // send a 200 (ok) to indicate it's available
            http_response_code(200);
        } else {
            // send a 409 (conflict) to indicate it's already in use
            http_response_code(409);
        }
        break;

    case 'login': // log the user in if it's valid

        // Rotate the session
        session_regenerate_id(true);

        $userId = checkLogin($username, $password);
        if ($userId) {
            // send a 200 (ok) to indicate that the user is logged in
            http_response_code(200);
            $_SESSION['Username'] = $username;
            $_SESSION['UserID'] = $userId;
        } else {
            // send a 401 (unauthorized) to indicate the login failed
            http_response_code(401);
            $_SESSION['Username'] = null;
            $_SESSION['UserID'] = null;
        }
        break;

    default:
        // Send a 400 (bad request) for unknown method
        http_response_code(400);
}

print json_encode($responseData);

//
//  FUNCTIONS
//

/**
 * Inserts a new user into the database
 *
 * @param string $username
 * @param string $password
 *
 * @return bool true if the user was added, false if the user already exists
 */
function addUser($username, $password) {
    global $mysql;

    $today        = date('Y-m-d H:i:s', time());
    $passwordSalt = uniqid();
    $passwordHash = hashPassword($password, $passwordSalt);

    $insert = "INSERT INTO users (Username, PasswordHash, PasswordSalt, DateCreated, LastLogin) VALUES ('" . mysql_real_escape_string($username, $mysql) . "', '" . mysql_real_escape_string($passwordHash, $mysql) . "', '" . mysql_real_escape_string($passwordSalt, $mysql) . "', '" . mysql_real_escape_string($today, $mysql) . "', '" . mysql_real_escape_string($today, $mysql) . "')";

    $result = mysql_query($insert, $mysql);
    if (!$result) {

        return false;
    }

    return true;
}

/**
 * Checks if a username and password are valid and updates the last login date & time
 *
 * @param string $username
 * @param string $password
 *
 * @return null
 */
function checkLogin($username, $password) {
    global $mysql;

    // See if we have a valid user
    $select = "SELECT * FROM users WHERE Username = '" . mysql_real_escape_string($username, $mysql). "'";
    $result = mysql_query($select, $mysql);
    if ($result && mysql_num_rows($result) === 1) {

        // See if the password matches
        $user = mysql_fetch_assoc($result);
        $passwordSalt = $user['PasswordSalt'];
        if ($user['PasswordHash'] === hashPassword($password, $passwordSalt)) {

            // Update the last login
            $today = date('Y-m-d H:i:s', time());
            $update = "UPDATE users SET LastLogin = '" . mysql_real_escape_string($today, $mysql). "' WHERE UserID = " . $user['UserID'];
            mysql_query($update, $mysql);

            // Finally, provide the caller with the UserID
            return $user['UserID'];
        }
    }

    return null;
}

/**
 * Does a very simple hashing of a password
 *
 * @param string $password
 * @param string $salt
 *
 * @return string A derived key based on the provided password and salt
 */
function hashPassword($password, $salt) {

    $iterations = 10;
    while ($iterations > 0) {
        $password = sha1($password . $salt);
        $iterations--;
    }

    return $password;
}

/**
 * Accepts a username and checks to see if it exists in the database
 *
 * @param string $username
 *
 * @return bool true if the username is available, false if it's taken already
 */
function isAvailable($username) {
    global $mysql;

    $select = "SELECT * FROM users WHERE Username = '" . mysql_real_escape_string($username, $mysql) . "'";
    $result = mysql_query($select, $mysql);
    if ($result && mysql_num_rows($result) >= 1) {

        return false;
    }

    return true;
}

