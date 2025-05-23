<?php
    include('session.php');

    if(isset($_GET['page_before_profile'])){
        $PageBeforeProfile = $_GET['page_before_profile'];
    } 

    if (isset($_SESSION['user_id'])) {
        $loginID = $_SESSION['user_id'];
        $host = "localhost";
        $username = "root";
        $password = "";
        $dbname = "rwdd_assignment";

        $conn = new mysqli($host, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "
            SELECT admin_id, admin_email, admin_name, admin_password 
            FROM administrator
            WHERE admin_id = ?
        ";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $loginID);
            $stmt->execute();
            $stmt->bind_result($adminID, $adminEmail, $adminName, $adminPW);

            if ($stmt->fetch()) {
                $_SESSION['adminName'] = $adminName;
                $_SESSION['adminID'] = $adminID;
                $_SESSION['adminPW'] = $adminPW;
                $_SESSION['adminEmail'] = $adminEmail;
            }
            $stmt->close();
        } else {
            echo "Error preparing the statement: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "User not logged in.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $existingPassword = $_POST['existingP'] ?? ''; // Use null coalescing operator for safety
        $newPassword = $_POST['newP'] ?? '';
        $confirmPassword = $_POST['confirmP'] ?? '';

        if ($existingPassword !== $_SESSION['adminPW']) {
            $_SESSION['error'] = "Existing password is incorrect!";
        } elseif ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = "New password and Confirm password do not match!";
        }else if ($existingPassword === $newPassword){
            $_SESSION['error'] = "New password cannot same with existing password!";
        } else {
            $host = "localhost";
            $username = "root";
            $password = "";
            $dbname = "rwdd_assignment";

            $conn = new mysqli($host, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "UPDATE administrator SET admin_password = ? WHERE admin_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("si", $newPassword, $_SESSION['adminID']);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Password updated successfully!";
                    $_SESSION['adminPW'] = $newPassword;
                } else {
                    $_SESSION['error'] = "Failed to update password.";
                }
                $stmt->close();
            } else {
                $_SESSION['error'] = "Error preparing the statement: " . $conn->error;
            }

            $conn->close();
        }

        header("Location: AdmProfilePage.php?page_before_profile=".$PageBeforeProfile);
        exit();
    }
    if (isset($_SESSION['error'])) {
        echo "<script>alert('" . $_SESSION['error'] . "');</script>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo "<script>alert('" . $_SESSION['success'] . "');</script>";
        unset($_SESSION['success']);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>+  Math</title>
</head>
<style>       
        @import url('https://fonts.googleapis.com/css2?family=Caveat:wght@400..700&family=Nanum+Myeongjo&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Sour+Gummy:wdth,wght@101.8,500&display=swap');
        *{
            font-family: "Open Sans", sans-serif;
            box-sizing:border-box;
        }

        tr{
        }

        table{
            font-size:20px;
            margin-bottom:20px;
            display:inline;
            
        }

        td{
            padding:20px;
        }

        .back{
            font-size: 16px; 
            background-color:white; 
            border-style:none; 
            padding-left:40px;
        }

        .back:hover{
            color:#D81B60;
        }

        .dropbtn {
            border-style:none; 
            background-color:transparent;
            padding: 10px;
            cursor: pointer;
        }

        .dropbtn:hover, .dropbtn:focus {
            background-color: #f7e3c8;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f1f1f1;
            margin-top:5px;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            left: -50px;
        }

        .dropdown-content a {
            color: black;
            padding: 0px 10px;
            text-decoration: none;
            display: flex;
            text-align:center;
            align-items:center;
            font-size: 14px;
            gap: 10px;
        }

        .dropdown-content a img{
        `width: 50px;
        height`:50px;
        }

        .dropdown-content a:hover {
            background-color:#f7e3c8;
            color: black;
        }

        .show {
            display:block;
        }
        
        @media only screen and (min-width: 768px) {
            .column1{
                width:20%;
            }

            .column2{
                width:3%;
            }

            .column3{
                width:77%;
            }

            .header {
                background-color: #D81B60; 
                color: white;
                text-align: center;
                padding: 20px;
                font-size: 24px;
                font-weight: bold;
                margin-top: 20px;
                margin-left:40px;
                margin-right:40px;
            }

            .container {
                display: flex;
                align-items: flex-start;
                margin-left:40px;
                margin-right:40px;
                background-color: #FBF8F5; 
            }

            .profile-image {
                width: 300px;
                height: 300px;
                display: flex;
                justify-content: center;
                align-items: center;
                border: 1px solid black;
                margin:90px 0px 90px 100px;
                background-color:white;
                padding-top:20px;
            }

            .profile-image img {
                width: 100%;
                height: 100%;
                object-fit: cover; 
            }


            .profile-details {
                flex: 1; 
                padding: 40px 80px;
                background-color: #FBF8F5;
                box-sizing: border-box;
            }

            .profilename {
                font-size: 30px;
                font-weight: bold;
                display: block; 
                padding: 10px 20px 10px 20px; 
                margin-bottom: 20px;
                background-color:#f7e3c8;
                margin-top:20px; 
            }

            .profile-details div {
                display: flex;             
                align-items: center;        
                margin-bottom: 20px; 
            }

            .value{
                font-weight: bold; 
            }

            .change-password {
                padding: 8px 20px;
                font-size: 16px;
                color:black;
                background-color: #f7e3c8;
                cursor: pointer;
                border-radius: 25px; 
                border:1px solid black;
            }

            .change-password:hover {
                opacity:0.8;
            }
            .highlighted-value {
                display: inline-block; 
                font-weight: normal;
            }
            .popCPbox {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                justify-content: center;
                align-items: center;
                z-index: 1000;
            }

            .CPcontent {
                background-color: white;
                padding: 50px;
                width: 600px;
                position: relative;
                transform: scale(0.8); 
                opacity: 0;
                z-index: 1100;
                transition: transform 0.3s ease-out, opacity 0.3s ease-out; 
            }
            .CPcontent h2 {
                text-align: center; 
                margin-bottom: 20px; 
            }

            .CPcontent label {
                display: block;
                text-align: left;
                margin-bottom: 5px; 
                margin-top: 10px; 
                font-weight: bold; 
            }
            .CPcontent form input {
                width: 100%; 
                padding: 5px;
                margin-bottom: 15px; 
                box-sizing: border-box; 
            }
            .CPcontent.show {
                transform: scale(1); 
                opacity: 1; 
            }
            
            .CPcontent form .form-buttons {
                display: flex; 
                justify-content: center; 
                gap: 20px; 
                margin-top: 20px; 
            }

            .CPcontent form button {
                padding: 10px 95px; 
                font-size: 14px;
                background-color: #D81B60;
                color: white;
                border: none;
                cursor: pointer;
            }

            .CPcontent form button:hover {
                opacity:0.8; 
            }

            .CPcontent.zoom-out {
                transform: scale(0.8);
                opacity: 0; 
            }
    }

    @media only screen and (min-width: 320px) and (max-width: 767px) {
        .column1{
            width:40%;
        }

        .column2{
            width:3%;
        }

        .column3{
            width:57%;
        }
        
        .back{
            font-size: 16px; 
            background-color:white; 
            border-style:none; 
            padding-left:20px;
        }
        .header {
            background-color: #D81B60; 
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
            margin-left:40px;
            margin-right:40px;
        }

        .container {
            display: flex;
            flex-direction: column;  
            height: auto;            
            margin-left:40px;
            background-color: #FBF8F5;
            margin-right:40px;
            margin-bottom:50px;
        }   

        .profile-image {
            width: auto;           
            height: 60%;              
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;  
            background-color: white;
            margin: 40px 100px;    
        }

        .profile-image img {
            width: 400px;             
            object-fit: cover;       
        }

        .profile-details {
            flex: 1; 
            padding: 20px 0px 20px 35px;
            box-sizing: border-box;
            background-color: #FBF8F5;             
        }

        .profilename {
            font-size: 30px;
            font-weight: bold;
            display: block; 
            padding: 10px 20px 10px 25px; 
            margin-bottom: 20px; 
            background-color:#f7e3c8;
            margin-right:40px; 
        }

        .profile-details div {
            display: flex;              
            align-items: center;        
            margin-bottom: 20px; 
            width: 100%;              
        }

        .value {
            font-weight: bold;
            padding-right:0px;     
            margin-right:0px;              
        }

        .highlighted-value {
            padding: 20px 0px 20px 10px;
            text-align: left; 
            font-weight: normal; 
        }

        .change-password {
            padding: 8px 20px;
            font-size: 16px;
            color: black;
            background-color: #f7e3c8;
            cursor: pointer;
            border-radius: 25px; 
            border:1px solid black;
            margin-top: 10px;
            margin-left:-10px;
        }

        .change-password:hover {
            opacity:0.8;
        }
        .popCPbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .CPcontent {
            background-color: white;
            padding: 50px;
            width: 600px;
            position: relative;
            transform: scale(0.8); 
            opacity: 0;
            z-index: 1100;
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;  
        }
        .CPcontent h2 {
            text-align: center; 
            margin-bottom: 40px; 
        }

        .CPcontent label {
            display: block;
            text-align: left;
            margin-bottom: 10px; 
            margin-top: 10px;  
            font-weight: bold; 
        }
        .CPcontent form input {
            width: 100%; 
            padding: 10px 5px;
            margin-bottom: 15px; 
            box-sizing: border-box; 
        }
        .CPcontent.show {
            transform: scale(1); 
            opacity: 1; 
        }
        
        .CPcontent form .form-buttons {
            display: flex; 
            justify-content: center; 
            gap: 20px; 
            margin-top: 20px; 
            width: 100%;
        }

        .CPcontent form button {
            padding: 10px 20px; 
            font-size: 14px;
            width: 80%;
            background-color: #D81B60;
            color: white;
            border: none;
            cursor: pointer;
        }

        .CPcontent form button:hover {
            opacity:0.8;
        }

        .CPcontent.zoom-out {
            transform: scale(0.8);
            opacity: 0; 
        }

    }
      
</style>
<body>
<div style="position:sticky; z-index:1; top:0; height: 73px;background-color:#FBF8F5;">
        <div style="float:left; padding:10px 10px 10px 30px;cursor:pointer;">
            <img src="logo.png" style="width:150px;" onclick="admHP()">
        </div>
        <div style="float:right; padding-right:40px;">
            <div class="dropdown">
                <button class="dropbtn" id="dropbtn" type="button" onclick="profileDropDown()">
                    <img src="stuprofilebtn.png" style="width:50px;">
                </button>
                <div id="myDropdown" class="dropdown-content">
                    <a href="AdmProfilePage.php?page_before_profile=AdmMainPage.php"> 
                        <img src="profileIcon.png">Profile
                    </a>
                    <a href="logoutPage.php?page_before_logout=AdmMainPage.php"> 
                        <img src="logoutIcon.png"> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
    <br>
    <button class="back" type="submit" name="back" onclick="window.location.href='<?php echo $PageBeforeProfile; ?>?user_id=<?php echo $_SESSION['user_id']; ?>'">< Back</button>
    <div class="header">
        Profile
    </div>    

    <div class="container">
        <div class="profile-image col-3">
            <img src="ProfilePageIcon.png" alt="Profile Icon">
        </div>

        <div class="profile-details col-9">
            <div class="profilename"><?php echo htmlspecialchars($_SESSION['adminName']); ?></div>
                <table style="width:100%;">
                    <tr>
                        <td class="column1 value">Admin ID</td> 
                        <td class="column2" style="font-weight: bold;">:</td>
                        <td class=" column highlighted-value"><?php echo htmlspecialchars($_SESSION['adminID']); ?></td>
                    </tr>
                    <tr>
                        <td class="column1 value">Admin Email</td> 
                        <td class="column2" style="font-weight: bold;">:</td>
                        <td class="column3 highlighted-value"><?php echo htmlspecialchars($_SESSION['adminEmail']); ?></td>
                    </tr>
                    <tr>
                        <td class="column1 value">Password:</td> 
                        <td class="column2" style="font-weight: bold;">:</td>
                        <td class="column3" style="padding:12px 20px;"><button id="CP" class="change-password" type="button"onclick="CPBox()">Change Password</button></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div id="CPbox" class="popCPbox">
        <div class="CPcontent">
            <h2>Change Password</h2>
            <form method="POST">
                <label for="existingP">Existing Password:</label>
                <input type="password" id="existingP" name="existingP" required>

                <label for="newP">New Password:</label>
                <input type="password" id="newP" name="newP" required>

                <label for="confirmP">Confirm New Password:</label>
                <input type="password" id="confirmP" name="confirmP" required>

                <div class="form-buttons">
                    <button type="submit">Change</button>
                    <button type="button" onclick="closeCPBox()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</body>
<script>
    function admHP(){
        window.location.href="AdmMainPage.php?user_id=<?php echo $_SESSION['user_id']; ?>";
    }
    function profileDropDown() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn') && !event.target.closest('.dropdown')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    };

    function CPBox() {
        const popup = document.getElementById("CPbox");
        const popupContent = document.querySelector(".CPcontent");

        popup.style.display = "flex"; 
        setTimeout(() => {
            popupContent.classList.add("show"); 
        }, 10); 
    }

    function closeCPBox() {
        const popup = document.getElementById("CPbox");
        const popupContent = document.querySelector(".CPcontent");

        popupContent.classList.remove("show"); 
        popupContent.classList.add("zoom-out"); 
        setTimeout(() => {
            popupContent.classList.remove("zoom-out"); 
            popup.style.display = "none";
        }, 300); 
    }

    window.addEventListener("click", function (event) {
        const popup = document.getElementById("CPbox");
        const popupContent = document.querySelector(".CPcontent");

        if (event.target === popup) {
            closeCPBox();
        }
    });
</script>

</html>