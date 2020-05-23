<?php
    include('database_connection.php');
    if(isset($_SESSION["type"]))
    {
        header("location: index.php");
    }
    $message = '';

    if(isset($_POST["login"]))
    {
        if(empty($_POST["user_email"]) || empty($_POST["user_password"]))
        {
            $message = "<label>Both Fields are required</label>"; 
        }
        else
        {
            $query= "
                SELECT * FROM user_details
                WHERE user_email = :user_email
            ";
            $statement = $connect->prepare($query);
            $statement->execute(
                array(
                    'user_email' => $_POST["user_email"]
                )
            );
            $count = $statement->rowCount();
            if($count>0)
            {
                $result = $statement->fetchAll();
                foreach($result as $row)
                {
                    if(password_verify($_POST["user_password"],$row["user_password"]))
                    {
                        $insert_query ="
                            INSERT INTO login_details(
                                user_id,last_activity) VALUES(
                                    :user_id,:last_avtivity)
                        ";
                        $statement = $connect->prepare($insert_query);
                        $statement->execute(
                            array(
                                'user_id' => $row["user_id"],
                                'last_activity' => date("Y-m-d H:i:s",STRTOTIME(date('h:i:sa')))
                            )
                        );
                        $login_id = $connect->lastInsertId();
                        if(!empty($login_id))
                        {
                            $_SESSION["type"] = $row["user_type"];
                            $_SESSION["login_id"] =$login_id;
                            header("location: index.php");
                        }
                    }
                    else
                    {
                        $message = $message = "<label>Wrong Password.</label>";
                    }
                }
            }
            else
            {
                $message = $message = "<label>Wrong Email Address</label>";
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login</title>
        <script src="https://ajax/googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7//js/boootstrap.min.js"></script>
        <style>
            body {font-family: Arial, Helvetica, sans-serif;}

            /* Full-width input fields */
            input[type=text], input[type=password] {
                width: 100%;
                padding: 12px 20px;
                margin: 8px 0;
                display: inline-block;
                border: 1px solid #ccc;
                box-sizing: border-box;
            }

            /* Set a style for all buttons */
            button {
                background-color: #4CAF50;
                color: white;
                padding: 14px 20px;
                margin: 8px 0;
                border: none;
                cursor: pointer;
                width: 100%;
            }

            button:hover {
                opacity: 0.8;
            }

            /* Extra styles for the cancel button */
            .cancelbtn {
                width: auto;
                padding: 10px 18px;
                background-color: #f44336;
            }

            /* Center the image and position the close button */
                .imgcontainer {
                text-align: center;
                margin: 24px 0 12px 0;
                position: relative;
            }

            img.avatar {
                width: 40%;
                border-radius: 50%;
            }

            .container {
                padding: 16px;
            }

            span.psw {
                float: right;
                padding-top: 16px;
            }

            /* The Modal (background) */
            .modal {
                display: none; /* Hidden by default */
                position: fixed; /* Stay in place */
                z-index: 1; /* Sit on top */
                left: 0;
                top: 0;
                width: 100%; /* Full width */
                height: 100%; /* Full height */
                overflow: auto; /* Enable scroll if needed */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
                padding-top: 60px;
            }

            /* Modal Content/Box */
            .modal-content {
                background-color: #fefefe;
                margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
                border: 1px solid #888;
                width: 80%; /* Could be more or less, depending on screen size */
            }

            /* The Close Button (x) */
            .close {
                position: absolute;
                right: 25px;
                top: 0;
                color: #000;
                font-size: 35px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: red;
                cursor: pointer;
            }

            /* Add Zoom Animation */
            .animate {
                -webkit-animation: animatezoom 0.6s;
                animation: animatezoom 0.6s
            }

            @-webkit-keyframes animatezoom {
                from {-webkit-transform: scale(0)} 
                to {-webkit-transform: scale(1)}
            }
        
            @keyframes animatezoom {
                from {transform: scale(0)} 
                to {transform: scale(1)}
            }

            /* Change styles for span and cancel button on extra small screens */
            @media screen and (max-width: 300px) {
                span.psw {
                    display: block;
                    float: none;
                }
                .cancelbtn {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
    <section>
        <h2>Testing Purpose</h2>

        <button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Login</button>

        <div id="id01" class="modal">
        
            <form class="modal-content animate" method="post">
                <div class="imgcontainer">
                    <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <img src="index.png" alt="Avatar" class="avatar">
                </div>

                <div class="container"><?php echo $message;?>
                    <label for="uname" class="label" name="Username"><b>Username</b></label>
                    <input type="text" placeholder="Enter Username" name="username" required>

                    <label for="psw" class="label" name ="Password"><b>Password</b></label>
                    <input type="password" placeholder="Enter Password" name="password" required>
                        
                    <button type="submit">Login</button>
                    <label>
                        <input type="checkbox" checked="checked" name="remember"> Remember me
                    </label>
                </div>

                <div class="container" style="background-color:#f1f1f1">
                    <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </form>
        </div>

        <script>
            // Get the modal
            var modal = document.getElementById('id01');

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
    </section>
    </body>
</html>