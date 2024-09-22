<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../build/config/db_conn.php';

//function debug_log($message) {
  //  error_log("[" . date("Y-m-d H:i:s") . "] " . $message);
//}

//debug_log("Script started. Request method: " . $_SERVER["REQUEST_METHOD"]);

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
  //  debug_log("Processing " . $_SERVER["REQUEST_METHOD"] . " request");

    $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
    $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
    $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
    $isAdmin = isset($_REQUEST['remember_me']) ? true : false;

    //debug_log("Username: $username, Email: $email, Is Admin: " . ($isAdmin ? 'Yes' : 'No'));

    $table = $isAdmin ? 'admin_login' : 'user_login';

    //debug_log("Using table: $table");

    $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ? AND email = ?");
    if (!$stmt) {
      //  debug_log("Prepare failed: " . $conn->error);
        die("An error occurred. Please try again later.");
    }
    
    $stmt->bind_param("ss", $username, $email);
    if (!$stmt->execute()) {
        //debug_log("Execute failed: " . $stmt->error);
        die("An error occurred. Please try again later.");
    }
    
    $result = $stmt->get_result();

    //debug_log("Query executed. Rows returned: " . $result->num_rows);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
      //  debug_log("Stored hashed password: " . $user['password']);
        //debug_log("Provided password: " . $password);
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
          //  debug_log("Password verified successfully");
            
            $_SESSION['user_id'] = $user[$isAdmin ? 'admin_id' : 'user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_group'] = $user['user_group'];
            $_SESSION['is_admin'] = $isAdmin;

            //debug_log("Session variables set. Attempting to redirect...");

            if ($isAdmin) {
              //  debug_log("Redirecting admin to ./dashboard.php");
                header("Location: ./dashboard.php");
            } else {
                //debug_log("Redirecting user to ../../../User/dashboard.php");
                header("Location: ../../User/watch/dashboard.php");
            }
            exit();
        } else {
            //debug_log("Password verification failed");
            //debug_log("password_verify() result: " . (password_verify($password, $user['password']) ? 'true' : 'false'));
            $error = "Invalid username or password";
        }
    } else {
        //debug_log("User not found: $username");
        $error = "Invalid username or password";
    }

    if (isset($error)) {
        $_SESSION['login_error'] = $error;
        //debug_log("Error set in session. Redirecting to login.php");
        header("Location: login.php");
        exit();
    }
} else {
    //debug_log("Invalid request method. Redirecting to login.php");
    header("Location: login.php");
    exit();
}

//debug_log("End of script reached without redirection");
?>