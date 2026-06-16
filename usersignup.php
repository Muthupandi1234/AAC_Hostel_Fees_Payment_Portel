<?php
include('includes/config.php');

if(isset($_POST['submit']))
{
    $regNo = $_POST['regno'];
    $department = $_POST['department'];
    $firstName = $_POST['firstname'];
    $middleName = $_POST['middlename'];
    $lastName = $_POST['lastname'];
    $gender = $_POST['gender'];
    $contactNo = $_POST['contact'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if($password != $cpassword){
        echo "<script>alert('Password & Confirm Password do not match');</script>";
    } else {

        // Profile Image Upload
        $photo = NULL;

        if(isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"] != ""){

            $targetDir = "uploads/";
            $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            // move file
            if(move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)){
                $photo = $fileName;
            }
        }

        // Set updation date
        $updationDate = date('Y-m-d H:i:s');

        $query = "INSERT INTO userregistration 
            (regNo, department, firstName, middleName, lastName, gender, contactNo, email, password, photo, updationDate) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssssssssss", 
            $regNo, 
            $department, 
            $firstName, 
            $middleName, 
            $lastName, 
            $gender, 
            $contactNo, 
            $email, 
            $password,
            $photo,
            $updationDate
        );

        if($stmt->execute()){
            echo "<script>alert('Account Created Successfully');</script>";
            echo "<script>window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error While Creating Account');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>User Signup | Hostel Automation</title>

<!-- Bootstrap 5 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    font-family: Arial;
}

.card {
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.btn-primary {
    background: #4e73df;
    border: none;
    font-size: 18px;
    padding: 10px;
    border-radius: 8px;
}

.btn-primary:hover {
    background: #2e59d9;
}

h3 {
    color: #4e73df;
    font-weight: 700;
}
</style>

</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card p-4">
                <h3 class="text-center mb-3">Create New Account</h3>

                <form method="POST" action="" enctype="multipart/form-data">

                    <label>Profile Photo</label>
                    <input type="file" name="photo" class="form-control mb-2">

                    <label>Register Number</label>
                    <input type="text" name="regno" class="form-control" required>

                    <label class="mt-2">First Name</label>
                    <input type="text" name="firstname" class="form-control" required>

                    <label class="mt-2">Middle Name (Optional)</label>
                    <input type="text" name="middlename" class="form-control">

                    <label class="mt-2">Last Name (Optional)</label>
                    <input type="text" name="lastname" class="form-control">

                    <label class="mt-2">Department</label>
                    <select name="department" class="form-control" required>
                        <option value="">-- Select Department --</option>
                        <option value="CSE">CSE</option>
                        <option value="IT">IT</option>
                        <option value="ECE">ECE</option>
                        <option value="EEE">EEE</option>
                        <option value="MECH">MECH</option>
                        <option value="CIVIL">CIVIL</option>
                    </select>

                    <label class="mt-2">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="">-- Select Gender --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>

                    <label class="mt-2">Contact Number</label>
                    <input type="text" name="contact" class="form-control" required>

                    <label class="mt-2">Email</label>
                    <input type="email" name="email" class="form-control" required>

                    <label class="mt-2">Password</label>
                    <input type="password" name="password" class="form-control" required>

                    <label class="mt-2">Confirm Password</label>
                    <input type="password" name="cpassword" class="form-control" required>

                    <button type="submit" name="submit" class="btn btn-primary mt-3 w-100">
                        Create Account
                    </button>

                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>
