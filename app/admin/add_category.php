<?php

require_once '../_base.php';


if (is_post()) {

    $categoryName = post('category_name');

    if ($categoryName === '') {
        temp('error', 'Category name is required.');
        redirect();
    } else {
        $sql = "INSERT INTO category (category_name) VALUES (?)";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$categoryName]);

        temp('info', 'Category added successfully.');
        redirect('admin_panel.php?page=category');
    }
}

?>

<div class="page-header">
    <h2>Add Category</h2>
</div>

<?php if ($error = temp('error')): ?>
    <p class="error"><?= encode($error) ?></p>
<?php endif; ?>

<div class="category-form">
    <form method="post">
        <input type="text" name="category_name" placeholder="Enter category name" required>

        <button type="submit" class="save-button">Save</button>

        <a href="admin_panel.php?page=category" class="cancel-button">← Back to Category</a>
    </form>
</div>