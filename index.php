<?php
require_once 'db.php';

$message = '';
$message_type = '';

// Variables for editing
$is_editing = false;
$edit_id = '';
$edit_name = '';
$edit_national_id = '';
$edit_phone = '';
$edit_email = '';
$edit_department = '';

// ========================
// 1. DELETE RECORD (CRUD: Delete)
// ========================
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$delete_id]);
        $message = "Employee record deleted successfully!";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Error deleting record: " . $e->getMessage();
        $message_type = "error";
    }
}

// ========================
// 2. LOAD RECORD FOR EDITING
// ========================
if (isset($_GET['edit'])) {
    $is_editing = true;
    $id_to_edit = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$id_to_edit]);
    $emp = $stmt->fetch();

    if ($emp) {
        $edit_id = $emp['id'];
        $edit_name = $emp['employee_name'];
        $edit_national_id = $emp['national_id'];
        $edit_phone = $emp['phone_number'];
        $edit_email = $emp['email'];
        $edit_department = $emp['department'];
    }
}

// ==========================================
// 3. INSERT OR UPDATE RECORD (CRUD: Create/Update)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id          = trim($_POST['id']);
    $name        = trim($_POST['employee_name']);
    $national_id = trim($_POST['national_id']);
    $phone       = trim($_POST['phone_number']);
    $email       = trim($_POST['email']);
    $department  = trim($_POST['department']);

    if (empty($id) || empty($name) || empty($national_id) || empty($phone) || empty($email) || empty($department)) {
        $message = "All fields are required!";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
        $message_type = "error";
    } else {
        if (isset($_POST['update_record'])) {
            // Update Existing Record
            try {
                $sql = "UPDATE employees SET employee_name = ?, national_id = ?, phone_number = ?, email = ?, department = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $national_id, $phone, $email, $department, $id]);
                $message = "Employee record updated successfully!";
                $message_type = "success";
                $is_editing = false;
            } catch (PDOException $e) {
                $message = "Update Error: " . $e->getMessage();
                $message_type = "error";
            }
        } else {
            // Insert New Record
            try {
                $sql = "INSERT INTO employees (id, employee_name, national_id, phone_number, email, department) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id, $name, $national_id, $phone, $email, $department]);
                $message = "Employee registered successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "Insert Error: " . $e->getMessage();
                $message_type = "error";
            }
        }
    }
}

// ========================
// 4. RETRIEVE ALL (CRUD: Read)
// ========================
$stmt = $pdo->query("SELECT * FROM employees ORDER BY created_at DESC");
$employees = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <!-- CSS Link -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Employee Management System</h2>

    <!-- Alert / Status Notification -->
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- HTML Registration / Update Form -->
    <div class="form-card">
        <h3><?php echo $is_editing ? 'Edit Employee Details' : 'Register New Employee'; ?></h3>
        
        <form id="employeeForm" action="index.php" method="POST">
            <div class="form-group">
                <label for="id">Employee ID:</label>
                <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($edit_id); ?>" <?php echo $is_editing ? 'readonly' : ''; ?> placeholder="e.g. EMP1001">
            </div>

            <div class="form-group">
                <label for="employee_name">Full Name:</label>
                <input type="text" id="employee_name" name="employee_name" value="<?php echo htmlspecialchars($edit_name); ?>" placeholder="e.g. Jane Doe">
            </div>

            <div class="form-group">
                <label for="national_id">National ID:</label>
                <input type="text" id="national_id" name="national_id" value="<?php echo htmlspecialchars($edit_national_id); ?>" placeholder="e.g. 12345678">
            </div>

            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($edit_phone); ?>" placeholder="e.g. +254712345678">
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($edit_email); ?>" placeholder="e.g. jane@company.com">
            </div>

            <div class="form-group">
                <label for="department">Department:</label>
                <select id="department" name="department">
                    <option value="">-- Select Department --</option>
                    <option value="Human Resources" <?php echo ($edit_department == 'Human Resources') ? 'selected' : ''; ?>>Human Resources</option>
                    <option value="Finance" <?php echo ($edit_department == 'Finance') ? 'selected' : ''; ?>>Finance</option>
                    <option value="Information Technology" <?php echo ($edit_department == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                    <option value="Marketing" <?php echo ($edit_department == 'Marketing') ? 'selected' : ''; ?>>Marketing</option>
                    <option value="Operations" <?php echo ($edit_department == 'Operations') ? 'selected' : ''; ?>>Operations</option>
                </select>
            </div>

            <div class="btn-group">
                <?php if ($is_editing): ?>
                    <button type="submit" name="update_record" class="btn btn-primary">Update</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="save_record" class="btn btn-primary">Save</button>
                    <button type="reset" class="btn btn-secondary">Clear</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Employee Records Table -->
    <h3>Employee Records</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Emp ID</th>
                    <th>Full Name</th>
                    <th>National ID</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($employees)): ?>
                    <?php foreach ($employees as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['employee_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['national_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['department']); ?></td>
                            <td class="action-btns">
                                <a href="index.php?edit=<?php echo urlencode($row['id']); ?>" class="btn btn-edit">Edit</a>
                                <a href="index.php?delete=<?php echo urlencode($row['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No employee records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript Link -->
<script src="validation.js"></script>
</body>
</html>