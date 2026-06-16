<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/config.php');
include('includes/checklogin.php');
check_admin_login();

$success = '';
$error = '';

// Load users for selection
$users = [];
$res = $mysqli->query("SELECT id, name, email FROM users ORDER BY name ASC");
while ($row = $res->fetch_assoc()) {
    $users[] = $row;
}

$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$existing_admission = null;

if ($selected_user_id) {
    $stmt = $mysqli->prepare("SELECT * FROM admission WHERE user_id = ? LIMIT 1");
    $stmt->bind_param('i', $selected_user_id);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r->num_rows > 0) $existing_admission = $r->fetch_assoc();
    $stmt->close();
}

if (isset($_POST['submit'])) {
    $user_id = intval($_POST['user_id']);
    $student_name = trim($_POST['student_name']);
    $reg_no = trim($_POST['reg_no']);
    $gender = $_POST['gender'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? null;
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $residential_address = trim($_POST['residential_address']);
    $course = trim($_POST['course']);
    $department = trim($_POST['department']);
    $year_of_study = trim($_POST['year_of_study']);
    $room_type = trim($_POST['room_type']);
    $room_number = trim($_POST['room_number']) ?: ('RM-' . rand(100,999));
    $mess_status = trim($_POST['mess_status']);
    $date_of_joining = $_POST['date_of_joining'] ?? null;
    $duration_of_stay = trim($_POST['duration_of_stay']);

    if (empty($user_id) || empty($student_name) || empty($reg_no) || empty($gender)) {
        $error = 'Please select a user and fill required fields (name, reg no, gender).';
    } else {
        // Check if admission exists for this user
        $stmt = $mysqli->prepare("SELECT id FROM admission WHERE user_id = ? LIMIT 1");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $r = $stmt->get_result();
        $has = $r->num_rows > 0;
        $stmt->close();

        if ($has) {
            $stmt = $mysqli->prepare("UPDATE admission SET student_name=?, reg_no=?, gender=?, date_of_birth=?, phone=?, email=?, residential_address=?, course=?, department=?, year_of_study=?, room_type=?, room_number=?, mess_status=?, date_of_joining=?, duration_of_stay=? WHERE user_id=?");
            $stmt->bind_param('sssssssssssssssi', $student_name, $reg_no, $gender, $date_of_birth, $phone, $email, $residential_address, $course, $department, $year_of_study, $room_type, $room_number, $mess_status, $date_of_joining, $duration_of_stay, $user_id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO admission (user_id, student_name, reg_no, gender, date_of_birth, phone, email, residential_address, course, department, year_of_study, room_type, room_number, mess_status, date_of_joining, duration_of_stay) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('isssssssssssssss', $user_id, $student_name, $reg_no, $gender, $date_of_birth, $phone, $email, $residential_address, $course, $department, $year_of_study, $room_type, $room_number, $mess_status, $date_of_joining, $duration_of_stay);
        }

        if ($stmt->execute()) {
            $stmt->close();
            header('Location: students.php?success=1');
            exit;
        } else {
            $error = 'Database error: unable to save admission.';
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Add Admission - Admin - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body{font-family: Poppins, sans-serif; background:#f5f7fa;} .main{margin:30px;}</style>
</head>
<body>
<div class="main container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="fas fa-user-plus"></i> Add / Edit Admission</h3>
        <a href="students.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label">Select User <span class="text-danger">*</span></label>
            <select name="user_id" class="form-select" required onchange="if(this.value) location.href='add_admission.php?user_id='+this.value;">
                <option value="">-- Choose user --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?php echo $u['id']; ?>" <?php echo ($selected_user_id == $u['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u['name'].' ('.$u['email'].')'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Student Name</label>
                <input type="text" name="student_name" class="form-control" value="<?php echo htmlspecialchars($existing_admission['student_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Register Number</label>
                <input type="text" name="reg_no" class="form-control" value="<?php echo htmlspecialchars($existing_admission['reg_no'] ?? ''); ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select</option>
                    <option value="Male" <?php echo (isset($existing_admission['gender']) && $existing_admission['gender']=='Male')?'selected':''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($existing_admission['gender']) && $existing_admission['gender']=='Female')?'selected':''; ?>>Female</option>
                    <option value="Other" <?php echo (isset($existing_admission['gender']) && $existing_admission['gender']=='Other')?'selected':''; ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($existing_admission['date_of_birth'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($existing_admission['phone'] ?? ''); ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($existing_admission['email'] ?? ''); ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Residential Address</label>
            <textarea name="residential_address" class="form-control"><?php echo htmlspecialchars($existing_admission['residential_address'] ?? ''); ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Course</label>
                <input type="text" name="course" class="form-control" value="<?php echo htmlspecialchars($existing_admission['course'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($existing_admission['department'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Year of Study</label>
                <input type="text" name="year_of_study" class="form-control" value="<?php echo htmlspecialchars($existing_admission['year_of_study'] ?? ''); ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Room Type</label>
                <select name="room_type" class="form-select">
                    <option value="">Select</option>
                    <option value="Single" <?php echo (isset($existing_admission['room_type']) && $existing_admission['room_type']=='Single')?'selected':''; ?>>Single</option>
                    <option value="Double" <?php echo (isset($existing_admission['room_type']) && $existing_admission['room_type']=='Double')?'selected':''; ?>>Double</option>
                    <option value="Triple" <?php echo (isset($existing_admission['room_type']) && $existing_admission['room_type']=='Triple')?'selected':''; ?>>Triple</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Room Number</label>
                <input type="text" name="room_number" class="form-control" value="<?php echo htmlspecialchars($existing_admission['room_number'] ?? ''); ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Mess Status</label>
                <select name="mess_status" class="form-select">
                    <option value="">Select</option>
                    <option value="Yes" <?php echo (isset($existing_admission['mess_status']) && $existing_admission['mess_status']=='Yes')?'selected':''; ?>>Yes</option>
                    <option value="No" <?php echo (isset($existing_admission['mess_status']) && $existing_admission['mess_status']=='No')?'selected':''; ?>>No</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Date of Joining</label>
                <input type="date" name="date_of_joining" class="form-control" value="<?php echo htmlspecialchars($existing_admission['date_of_joining'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Duration of Stay</label>
                <input type="text" name="duration_of_stay" class="form-control" value="<?php echo htmlspecialchars($existing_admission['duration_of_stay'] ?? ''); ?>">
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Admission</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
