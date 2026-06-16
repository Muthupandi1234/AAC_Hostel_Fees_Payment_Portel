<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('includes/config.php');
include('includes/checklogin.php');
check_login();

/* ===============================
   STEP 1: Validate Session
================================ */
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$success = '';
$error = '';

/* ===============================
   STEP 2: Verify User Exists
================================ */
$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$user_profile = $userResult->fetch_assoc();
$stmt->close();

/* ===============================
   STEP 3: Check Existing Admission
================================ */
$existing_admission = null;
$stmt = $mysqli->prepare(
    "SELECT * FROM admission WHERE user_id = ? LIMIT 1"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $existing_admission = $res->fetch_assoc();
}
$stmt->close();

/* ===============================
   STEP 4: Auto Room Number
================================ */
$result = $mysqli->query("SELECT MAX(id) as last_id FROM admission");
$row = $result->fetch_assoc();
$next_id = $row['last_id'] + 1;
$room_number = 'RM-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

/* ===============================
   STEP 5: Form Submission
================================ */
if (isset($_POST['submit'])) {

    $student_name = trim($_POST['student_name']);
    $reg_no = trim($_POST['reg_no']);
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $residential_address = trim($_POST['residential_address']);
    $course = trim($_POST['course']);
    $department = trim($_POST['department']);
    $year_of_study = trim($_POST['year_of_study']);
    $room_type = trim($_POST['room_type']);
    $mess_status = trim($_POST['mess_status']);
    $date_of_joining = $_POST['date_of_joining'];
    $duration_of_stay = trim($_POST['duration_of_stay']);

    /* ===============================
       Validation
    ================================ */
    if (
        empty($student_name) || empty($reg_no) || empty($gender) ||
        empty($date_of_birth) || empty($phone) || empty($email) ||
        empty($residential_address) || empty($course) ||
        empty($department) || empty($year_of_study) ||
        empty($room_type) || empty($mess_status) ||
        empty($date_of_joining) || empty($duration_of_stay)
    ) {
        $error = "All fields are required";
    } else {

        if ($existing_admission) {

            /* ===============================
               UPDATE
            ================================ */
            $stmt = $mysqli->prepare("
                UPDATE admission SET
                student_name=?, reg_no=?, gender=?, date_of_birth=?,
                phone=?, email=?, residential_address=?, course=?,
                department=?, year_of_study=?, room_type=?,
                mess_status=?, date_of_joining=?, duration_of_stay=?
                WHERE user_id=?
            ");

            $stmt->bind_param(
                "ssssssssssssssi",
                $student_name,
                $reg_no,
                $gender,
                $date_of_birth,
                $phone,
                $email,
                $residential_address,
                $course,
                $department,
                $year_of_study,
                $room_type,
                $mess_status,
                $date_of_joining,
                $duration_of_stay,
                $user_id
            );

        } else {

            /* ===============================
               INSERT (FK SAFE)
            ================================ */
            $stmt = $mysqli->prepare("
                INSERT INTO admission
                (user_id, student_name, reg_no, gender, date_of_birth,
                phone, email, residential_address, course, department,
                year_of_study, room_type, room_number, mess_status,
                date_of_joining, duration_of_stay)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ");

            $stmt->bind_param(
                "isssssssssssssss",
                $user_id,
                $student_name,
                $reg_no,
                $gender,
                $date_of_birth,
                $phone,
                $email,
                $residential_address,
                $course,
                $department,
                $year_of_study,
                $room_type,
                $room_number,
                $mess_status,
                $date_of_joining,
                $duration_of_stay
            );
        }

        if ($stmt->execute()) {
            // After saving admission, redirect to the payment page to pay the admission fee
            header("Location: admission-fee-payment.php?show_pay=1");
            exit;
        } else {
            $error = "Unable to save your information. Please try again or contact support.";
        }

        $stmt->close();
    }
}

/* ===============================
   Success Message
================================ */
if (isset($_GET['success'])) {
    $success = "Your admission information has been saved successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            color: #333;
        }
        

        
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1e3c72;
            margin: 0;
        }
        
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        @media (max-width: 768px) {

    .form-card {
        padding: 20px;
    }

    .header {
        padding: 15px;
        text-align: center;
    }

    .header h1 {
        font-size: 20px;
    }

    .btn-submit {
        width: 100%;
    }

}
    </style>
</head>
<body>
    <!-- Include User Sidebar -->
    <?php include('includes/user_sidebar.php'); ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1><i class="fas fa-file-alt"></i> Admission Form</h1>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <strong><?php echo htmlspecialchars($success); ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <strong><?php echo htmlspecialchars($error); ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="form-card">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Student Name <span class="text-danger">*</span></label>
                        <input type="text" name="student_name" class="form-control" value="<?php echo htmlspecialchars($existing_admission['student_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Register Number <span class="text-danger">*</span></label>
                        <input type="text" name="reg_no" class="form-control" value="<?php echo htmlspecialchars($existing_admission['reg_no'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="Male" <?php echo (isset($existing_admission['gender']) && $existing_admission['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($existing_admission['gender']) && $existing_admission['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo (isset($existing_admission['gender']) && $existing_admission['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($existing_admission['date_of_birth'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($existing_admission['phone'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control"
value="<?php echo htmlspecialchars($user_profile['email']); ?>"
readonly>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Residential Address <span class="text-danger">*</span></label>
                    <textarea name="residential_address" class="form-control" rows="3" required><?php echo htmlspecialchars($existing_admission['residential_address'] ?? ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <input type="text" name="course" class="form-control" value="<?php echo htmlspecialchars($existing_admission['course'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($existing_admission['department'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Year of Study <span class="text-danger">*</span></label>
                        <input type="text" name="year_of_study" class="form-control" value="<?php echo htmlspecialchars($existing_admission['year_of_study'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Type <span class="text-danger">*</span></label>
                        <select name="room_type" class="form-select" required>
                            <option value="">Select Room Type</option>
                            <option value="Single" <?php echo (isset($existing_admission['room_type']) && $existing_admission['room_type'] == 'Single') ? 'selected' : ''; ?>>Single</option>
                            <option value="Double" <?php echo (isset($existing_admission['room_type']) && $existing_admission['room_type'] == 'Double') ? 'selected' : ''; ?>>Double</option>
                            <option value="Triple" <?php echo (isset($existing_admission['room_type']) && $existing_admission['room_type'] == 'Triple') ? 'selected' : ''; ?>>Triple</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mess Status <span class="text-danger">*</span></label>
                        <select name="mess_status" class="form-select" required>
                            <option value="">Select Mess Status</option>
                            <option value="Yes" <?php echo (isset($existing_admission['mess_status']) && $existing_admission['mess_status'] == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                            <option value="No" <?php echo (isset($existing_admission['mess_status']) && $existing_admission['mess_status'] == 'No') ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Joining <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_joining" class="form-control" value="<?php echo htmlspecialchars($existing_admission['date_of_joining'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Duration of Stay <span class="text-danger">*</span></label>
                        <input type="text" name="duration_of_stay" class="form-control" value="<?php echo htmlspecialchars($existing_admission['duration_of_stay'] ?? ''); ?>" placeholder="e.g., 1 Year" required>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" name="submit" class="btn btn-submit">
                        <i class="fas fa-save"></i> <?php echo $existing_admission ? 'Update Admission' : 'Submit Admission'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
document.querySelector("form").addEventListener("submit", function() {
    const btn = document.querySelector(".btn-submit");
    btn.disabled = true;
    btn.innerHTML = "Processing...";
});
</script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
