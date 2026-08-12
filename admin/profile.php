<?php
require_once '../_base.php';


// ======================================================
// ERROR ARRAY
// ======================================================

$_err = [];


// ======================================================
// CHECK LOGIN
// ======================================================

if (!isset($_SESSION['user'])) {
    die('Admin not logged in');
}

$title = 'Admin Profile';
$admin = $_SESSION['user'];


// ======================================================
// SUPPORT BOTH OBJECT AND ARRAY SESSION USER
// ======================================================

function admin_value($admin, $key, $default = '')
{
    if (is_object($admin)) {
        return $admin->$key ?? $default;
    }

    if (is_array($admin)) {
        return $admin[$key] ?? $default;
    }

    return $default;
}


function update_admin_session($key, $value)
{
    if (is_object($_SESSION['user'])) {
        $_SESSION['user']->$key = $value;
    }
    elseif (is_array($_SESSION['user'])) {
        $_SESSION['user'][$key] = $value;
    }
}


// ======================================================
// CURRENT ADMIN INFORMATION
// ======================================================

$user_id = admin_value($admin, 'user_id');
$name = admin_value($admin, 'name');
$email = admin_value($admin, 'email');
$role = admin_value($admin, 'role', 'admin');
$gender = admin_value($admin, 'gender', 'N/A');
$photo = admin_value($admin, 'profile_photo');


// ======================================================
// CHANGE PASSWORD
// ======================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['change_password'])
) {

    $current = trim($_POST['current_password'] ?? '');
    $new = trim($_POST['new_password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');


    // Validate current password
    if ($current === '') {
        $_err['password'] = 'Current password is required.';
    }

    // Validate new password
    elseif ($new === '') {
        $_err['password'] = 'New password is required.';
    }

    elseif (strlen($new) < 6) {
        $_err['password'] = 'Password must be at least 6 characters.';
    }

    elseif ($confirm === '') {
        $_err['password'] = 'Please confirm your new password.';
    }

    elseif ($new !== $confirm) {
        $_err['password'] = 'Passwords do not match.';
    }

    else {

        // Get password from database
        $stm = $_db->prepare(
            'SELECT password
             FROM user
             WHERE user_id = ?'
        );

        $stm->execute([$user_id]);

        $db_password = $stm->fetchColumn();


        if ($db_password === false) {

            $_err['password'] = 'User not found.';
        }

        elseif (sha1($current) !== $db_password) {

            $_err['password'] = 'Current password incorrect.';
        }

        else {

            // Keep SHA1 because your existing system uses SHA1
            $hashed_password = sha1($new);

            $stm = $_db->prepare(
                'UPDATE user
                 SET password = ?
                 WHERE user_id = ?'
            );

            $stm->execute([
                $hashed_password,
                $user_id
            ]);

            temp(
                'info',
                'Password updated successfully.'
            );

            redirect(
                'admin_panel.php?page=profile'
            );
        }
    }
}


// ======================================================
// UPDATE PROFILE
// ======================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['update_profile'])
) {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $f = get_file('photo');


    // ==================================================
    // NAME VALIDATION
    // ==================================================

    if ($name === '') {

        $_err['name'] = 'Name is required.';
    }

    elseif (strlen($name) > 100) {

        $_err['name'] = 'Maximum 100 characters.';
    }


    // ==================================================
    // EMAIL VALIDATION
    // ==================================================

    if ($email === '') {

        $_err['email'] = 'Email is required.';
    }

    elseif (strlen($email) > 100) {

        $_err['email'] = 'Maximum 100 characters.';
    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $_err['email'] = 'Invalid email address.';
    }

    else {

        // Check duplicate email
        $stm = $_db->prepare(
            'SELECT COUNT(*)
             FROM user
             WHERE email = ?
             AND user_id != ?'
        );

        $stm->execute([
            $email,
            $user_id
        ]);

        if ($stm->fetchColumn() > 0) {

            $_err['email'] = 'Email already exists.';
        }
    }


    // ==================================================
    // PHOTO VALIDATION
    // ==================================================

    if ($f) {

        if (!str_starts_with($f->type, 'image/')) {

            $_err['photo'] = 'File must be an image.';
        }

        elseif ($f->size > 1024 * 1024) {

            $_err['photo'] = 'Maximum photo size is 1MB.';
        }
    }


    // ==================================================
    // SAVE PROFILE
    // ==================================================

    if (empty($_err)) {


        // Upload new photo
        if ($f) {

            $old_photo = $photo;


            // Save new photo first
            $new_photo = save_photo(
                $f,
                __DIR__ . '/../photo'
            );


            if ($new_photo) {

                $photo = $new_photo;


                // Delete old photo
                if (
                    $old_photo !== ''
                    && file_exists(
                        __DIR__
                        . '/../photo/'
                        . $old_photo
                    )
                ) {

                    unlink(
                        __DIR__
                        . '/../photo/'
                        . $old_photo
                    );
                }
            }
        }


        // Update database
        $stm = $_db->prepare(
            'UPDATE user
             SET name = ?,
                 email = ?,
                 profile_photo = ?
             WHERE user_id = ?'
        );

        $stm->execute([
            $name,
            $email,
            $photo,
            $user_id
        ]);


        // Update session
        update_admin_session(
            'name',
            $name
        );

        update_admin_session(
            'email',
            $email
        );

        update_admin_session(
            'profile_photo',
            $photo
        );


        temp(
            'info',
            'Profile updated successfully.'
        );

        redirect(
            'admin_panel.php?page=profile'
        );
    }
}


// ======================================================
// PROFILE PHOTO PATH
// ======================================================

if ($photo !== '') {

    $displayPhoto =
        '../photo/' . $photo;
}

else {

    $displayPhoto =
        '../images/favicon.png';
}


// ======================================================
// HTML ESCAPE FUNCTION
// ======================================================

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>


<div class="profile-page">


    <!-- =================================================
         PAGE HEADER
    ================================================== -->

    <div class="page-header">

        <h1>Admin Profile</h1>

        <p class="muted">
            Update your administrator details and profile photo.
        </p>

    </div>




    <div class="profile-grid">


        <!-- =================================================
             LEFT PROFILE CARD
        ================================================== -->

        <div class="profile-card">


            <img
                src="<?= e($displayPhoto) ?>"
                alt="Admin Profile Photo"
            >


            <h2>
                <?= e(admin_value($_SESSION['user'], 'name')) ?>
            </h2>


            <p class="muted">

                <?= e(
                    ucfirst(
                        admin_value(
                            $_SESSION['user'],
                            'role',
                            'admin'
                        )
                    )
                ) ?>

            </p>



            <div class="profile-info">


                <div>

                    <strong>Email</strong>

                    <span>
                        <?= e(
                            admin_value(
                                $_SESSION['user'],
                                'email'
                            )
                        ) ?>
                    </span>

                </div>



                <div>

                    <strong>User ID</strong>

                    <span>
                        <?= e($user_id) ?>
                    </span>

                </div>



                <div>

                    <strong>Gender</strong>

                    <span>
                        <?= e($gender) ?>
                    </span>

                </div>


            </div>


        </div>



        <!-- =================================================
             RIGHT SIDE
        ================================================== -->

        <div class="profile-form">


            <!-- =================================================
                 UPDATE PROFILE
            ================================================== -->

            <form
                method="post"
                enctype="multipart/form-data"
            >


                <h3>Profile Information</h3>



                <!-- NAME -->

                <div class="form-group">

                    <label for="name">
                        Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        maxlength="100"
                        value="<?= e($name) ?>"
                    >


                    <?php if (isset($_err['name'])): ?>

                        <span class="error">
                            <?= e($_err['name']) ?>
                        </span>

                    <?php endif; ?>

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="100"
                        value="<?= e($email) ?>"
                    >


                    <?php if (isset($_err['email'])): ?>

                        <span class="error">
                            <?= e($_err['email']) ?>
                        </span>

                    <?php endif; ?>

                </div>



                <!-- PHOTO -->

                <div class="form-group upload">

                    <label>
                        Profile Photo
                    </label>


                    <label class="upload">

                        <input
                            type="file"
                            name="photo"
                            accept="image/*"
                            hidden
                        >


                        <img
                            src="<?= e($displayPhoto) ?>"
                            alt="Profile Photo"
                        >

                    </label>


                    <?php if (isset($_err['photo'])): ?>

                        <span class="error">
                            <?= e($_err['photo']) ?>
                        </span>

                    <?php endif; ?>

                </div>



                <!-- BUTTON -->

                <div class="form-actions">

                    <button
                        type="submit"
                        name="update_profile"
                        value="1"
                    >

                        Save Changes

                    </button>


                    <button
                        type="reset"
                        class="reset-btn"
                    >

                        Reset

                    </button>

                </div>


            </form>



            <hr>



            <!-- =================================================
                 CHANGE PASSWORD
            ================================================== -->

            <form method="post">


                <h3>
                    Change Password
                </h3>



                <!-- CURRENT PASSWORD -->

                <div class="form-group">

                    <label for="current_password">
                        Current Password
                    </label>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                    >

                </div>



                <!-- NEW PASSWORD -->

                <div class="form-group">

                    <label for="new_password">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        autocomplete="new-password"
                    >

                </div>



                <!-- CONFIRM PASSWORD -->

                <div class="form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        autocomplete="new-password"
                    >


                    <?php if (isset($_err['password'])): ?>

                        <span class="error">
                            <?= e($_err['password']) ?>
                        </span>

                    <?php endif; ?>

                </div>



                <!-- BUTTON -->

                <div class="form-actions">

                    <button
                        type="submit"
                        name="change_password"
                        value="1"
                    >

                        Change Password

                    </button>

                </div>


            </form>


        </div>


    </div>

</div>