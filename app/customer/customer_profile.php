<?php
require_once '../_base.php';
$title = 'My Profile';
$_title = '';
include 'customer_header.php';

// Redirect if not logged in
if (!$_user) {
    redirect('/login.php');
}

// Get user data
$user_id = $_user->user_id;
$stm = $_db->prepare("SELECT * FROM user WHERE user_id = ?");
$stm->execute([$user_id]);
$user = $stm->fetch();

// Handle form submission
if (is_post()) {
    $email = post('email');
    $name = post('name');
    $photo = $user->profile_photo;
    
    // 🔥 Handle photo upload
    $f = get_file('profile_photo');
    if ($f) {
        // Check if SimpleImage exists
        if (file_exists('../lib/SimpleImage.php')) {
            $photo = save_photo($f, '../photo', 300, 300);
        } else {
            // SimpleImage not found - just save the file directly
            $photo = uniqid() . '.jpg';
            move_uploaded_file($f->tmp_name, "../photo/$photo");
        }
    }
    
    // Update user
    $stm = $_db->prepare("UPDATE user SET email = ?, name = ?, profile_photo = ? WHERE user_id = ?");
    $stm->execute([$email, $name, $photo, $user_id]);
    
    // Update session
    $_user->email = $email;
    $_user->name = $name;
    $_user->profile_photo = $photo;
    $_SESSION['user'] = $_user;
    
    temp('info', 'Profile updated successfully!');
    redirect('/customer/customer_profile.php');
}
?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h1 class="auth-title">My Profile</h1>
        <p class="auth-subtitle">Update your personal information</p>
        
        <!-- 🔥 重要：添加 enctype -->
        <form method="post" class="auth-form" enctype="multipart/form-data">
            
            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= encode($user->email) ?>" required>
                <?php err('email'); ?>
            </div>
            
            <!-- Name -->
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?= encode($user->name) ?>" required>
                <?php err('name'); ?>
            </div>
            
            <!-- Photo -->
            <div class="form-group">
                <label>Profile Photo</label>
                <label class="upload" tabindex="0">
                    <?php html_file('profile_photo', 'image/*', 'hidden'); ?>
                    <img src="/photo/<?= encode($user->profile_photo ?: 'default.png') ?>" 
                         alt="Profile Photo" 
                         id="profilePreview"
                         style="width:120px; height:120px; object-fit:cover; border-radius:50%; cursor:pointer; border:2px solid #ddd;">
                </label>
                <small style="color:#8D6E63; font-size:12px; display:block; text-align:center;">
                    Click image to upload new photo
                </small>
                <?php err('photo'); ?>
            </div>
            
            <!-- Buttons -->
            <div class="form-actions">
                <div class="form-actions-row">
                <button type="submit" class="btn-save">Submit</button>
                <button type="reset" class="btn-reset">Reset</button>
            </div>
            <a href="/" class="btn-back-home"> ← Back To Home</a>
            </div>
            
        </form>
    </div>
</div>
