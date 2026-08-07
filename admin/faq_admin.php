<?php
require 'db_connect.php';
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminlogin.php');
    exit();
}
$adminName = $_SESSION['admin_name'] ?? 'Administrator';

// Handle add FAQ
if (isset($_POST['add_faq'])) {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $answer = mysqli_real_escape_string($conn, $_POST['answer']);
    $conn->query("INSERT INTO faqs (question, answer) VALUES ('$question', '$answer')");
}

// Handle edit FAQ
if (isset($_POST['edit_faq'])) {
    $id = intval($_POST['id']);
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $answer = mysqli_real_escape_string($conn, $_POST['answer']);
    $conn->query("UPDATE faqs SET question='$question', answer='$answer' WHERE id=$id");
}

// Handle delete FAQ
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM faqs WHERE id=$id");
}

// Fetch all FAQs
$result = $conn->query("SELECT * FROM faqs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage FAQs - NIITDF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="adminacc.css">
    <style>
        .container { max-width: 900px; margin: 40px auto; }
        .card { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .table th { background-color: #f8f9fa; }
        .edit-form { display: none; background-color: #f8f9fa; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <?php include 'admininterface.php'; ?>
    <div class="header">
        <button class="show-side" onclick="showSidebar()" aria-label="Open sidebar" style="background:none;border:none;cursor:pointer;">
            <span style="display:inline-block;width:28px;height:22px;position:relative;">
            <span style="display:block;height:4px;width:100%;background:#333;border-radius:2px;position:absolute;top:0;left:0;transition:.3s;"></span>
            <span style="display:block;height:4px;width:80%;background:#333;border-radius:2px;position:absolute;top:9px;left:0;transition:.3s;"></span>
            </span>
        </button>
        <div><h1>Manage FAQs</h1></div>
        <div class="admin-info">
            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($adminName); ?>
        </div>
    </div>
    <div class="main-content container" style="margin-top:70px;">
        <div class="row">
            <div class="col-md-12">
                <div class="card p-4 mb-4">
                    <h2>Add New FAQ</h2>
                    <form method="post">
                        <div class="mb-3">
                            <label for="question" class="form-label">Question</label>
                            <input type="text" name="question" id="question" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="answer" class="form-label">Answer</label>
                            <textarea name="answer" id="answer" rows="4" class="form-control" required></textarea>
                        </div>
                        <button type="submit" name="add_faq" class="btn btn-primary">Add FAQ</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card p-4">
                    <h2>Existing FAQs</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Answer</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['question']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($row['answer'])); ?></td>
                                        <td>
                                            <a href="#<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" onclick="toggleEditForm(<?php echo $row['id']; ?>)">Edit</a>
                                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this FAQ?');">Delete</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <form method="post" id="edit-form-<?php echo $row['id']; ?>" class="edit-form">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="question-<?php echo $row['id']; ?>" class="form-label">Question</label>
                                                    <input type="text" name="question" id="question-<?php echo $row['id']; ?>" class="form-control" value="<?php echo htmlspecialchars($row['question']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="answer-<?php echo $row['id']; ?>" class="form-label">Answer</label>
                                                    <textarea name="answer" id="answer-<?php echo $row['id']; ?>" rows="4" class="form-control" required><?php echo htmlspecialchars($row['answer']); ?></textarea>
                                                </div>
                                                <button type="submit" name="edit_faq" class="btn btn-success">Update FAQ</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
    <script>
        function toggleEditForm(id) {
            const form = document.getElementById('edit-form-' + id);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>