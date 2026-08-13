<?php

require_once __DIR__ . '/../_base.php';

$_err = [];

$user_id = $_GET['id'] ?? '';

if ($user_id === '' || !ctype_digit((string)$user_id)) {
    redirect('admin_panel.php?page=users');
}

$stm = $_db->prepare(
    'SELECT *
     FROM user
     WHERE user_id = ?'
);

$stm->execute([$user_id]);

$user = $stm->fetch();

if (!$user) {
    redirect('admin_panel.php?page=users');
}

$name = $user->name;
$email = $user->email;
$gender = $user->gender ?? '';
$role = $user->role ?? 'customer';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $role = $_POST['role'] ?? 'customer';


    if ($name === '') {
        $_err['name'] = 'Name is required.';
    }

    if ($email === '') {

        $_err['email'] = 'Email is required.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $_err['email'] = 'Invalid email.';
    }


    if (!in_array($gender, ['F', 'M'])) {
        $_err['gender'] = 'Please select gender.';
    }


    if (!in_array($role, ['admin', 'customer'])) {
        $_err['role'] = 'Invalid role.';
    }


    if (empty($_err)) {

        $stm = $_db->prepare(
            'UPDATE user
             SET
                name = ?,
                email = ?,
                gender = ?,
                role = ?
             WHERE user_id = ?'
        );

        $stm->execute([
            $name,
            $email,
            $gender,
            $role,
            $user_id
        ]);

        temp('info', 'User updated successfully.');

        redirect('admin_panel.php?page=users');
    }
}

?>

<div class="edit-user-page">

    <div class="page-header">
        <h1>Edit User</h1>

        <p class="muted">
            Update user information.
        </p>
    </div>

    <div class="edit-user-card">

        <form method="post">

            <div class="form-group">
                <label>Name</label>

                <input
                    type="text"
                    name="name"
                    value="<?= encode($name) ?>"
                >

                <?php if (isset($_err['name'])): ?>
                    <span class="error">
                        <?= encode($_err['name']) ?>
                    </span>
                <?php endif; ?>
            </div>


            <div class="form-group">
                <label>Email</label>

                <input
                    type="text"
                    name="email"
                    value="<?= encode($email) ?>"
                >

                <?php if (isset($_err['email'])): ?>
                    <span class="error">
                        <?= encode($_err['email']) ?>
                    </span>
                <?php endif; ?>
            </div>


            <div class="form-group">
                <label>Gender</label>

                <select name="gender">

                    <option value="">
                        -- Select Gender --
                    </option>

                    <option
                        value="F"
                        <?= $gender === 'F' ? 'selected' : '' ?>
                    >
                        Female
                    </option>

                    <option
                        value="M"
                        <?= $gender === 'M' ? 'selected' : '' ?>
                    >
                        Male
                    </option>

                </select>

                <?php if (isset($_err['gender'])): ?>
                    <span class="error">
                        <?= encode($_err['gender']) ?>
                    </span>
                <?php endif; ?>
            </div>


            <div class="form-group">
                <label>Role</label>

                <select name="role">

                    <option
                        value="customer"
                        <?= $role === 'customer' ? 'selected' : '' ?>
                    >
                        Customer
                    </option>

                    <option
                        value="admin"
                        <?= $role === 'admin' ? 'selected' : '' ?>
                    >
                        Admin
                    </option>

                </select>

                <?php if (isset($_err['role'])): ?>
                    <span class="error">
                        <?= encode($_err['role']) ?>
                    </span>
                <?php endif; ?>
            </div>


            <div class="form-actions">

                <button type="submit">
                    Save Changes
                </button>

                <a
                    href="admin_panel.php?page=users"
                    class="cancel-user-btn"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>