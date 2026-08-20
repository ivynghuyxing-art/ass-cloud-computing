<?php

require_once '../_base.php';


// =====================================
// ADD / EDIT / DELETE
// =====================================

if (is_post()) {

    $action = post('action');


    // =================================
    // ADD CATEGORY
    // =================================

    if ($action === 'add') {

        $categoryName = post('category_name');

        if ($categoryName === '') {

            temp('error', 'Category name is required.');

        } else {

            $sql = "INSERT INTO category (category_name)
                    VALUES (?)";

            $stmt = $_db->prepare($sql);
            $stmt->execute([$categoryName]);

            temp('info', 'Category added successfully.');
        }

        redirect();
    }


    // =================================
    // EDIT CATEGORY
    // =================================

    if ($action === 'edit') {

        $categoryId = post('category_id');
        $categoryName = post('category_name');

        if ($categoryName === '') {

            temp('error', 'Category name is required.');

        } else {

            $sql = "UPDATE category
                    SET category_name = ?
                    WHERE category_id = ?";

            $stmt = $_db->prepare($sql);

            $stmt->execute([
                $categoryName,
                $categoryId
            ]);

            temp('info', 'Category updated successfully.');
        }

        redirect();
    }


    // =================================
    // DELETE CATEGORY
    // =================================

    if ($action === 'delete') {

        $categoryId = post('category_id');

        if ($categoryId !== '') {

            $sql = "DELETE FROM category
                    WHERE category_id = ?";

            $stmt = $_db->prepare($sql);
            $stmt->execute([$categoryId]);

            temp('info', 'Category deleted successfully.');
        }

        redirect($_SERVER['REQUEST_URI']);
    }
}


// =====================================
// GET CATEGORY LIST
// =====================================

$stm = $_db->query(
    "SELECT category_id, category_name
     FROM category
     ORDER BY category_name"
);

$categories = $stm->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Category Management</title>

    <link rel="stylesheet" href="css/style.css">
    <script src="js/app.js"></script>

</head>


<body>

<div class="users-page">

    <!-- HEADER -->
    <div class="page-header category-management-header">

        <div>
            <h1>Category Management</h1>

            <p class="muted">
                View and manage book categories in the library system.
            </p>
        </div>

        <a
            href="admin_panel.php?page=add_category"
            class="add-category-btn"
        >
            + Add Category
        </a>

    </div>


    <!-- CATEGORY CARD -->
    <div class="users-card">

        <?php if (empty($categories)): ?>

            <p>No categories found.</p>

        <?php else: ?>

            <div class="table-wrapper">

                <table class="user-table">

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($categories as $category): ?>

                            <tr>

                                <!-- ID -->
                                <td>
                                    <?= encode($category->category_id) ?>
                                </td>


                                <!-- CATEGORY NAME -->
                                <td>

                                    <span
                                        id="name-<?= $category->category_id ?>"
                                    >
                                        <?= encode($category->category_name) ?>
                                    </span>


                                    <!-- EDIT FORM -->
                                    <form
                                        method="post"
                                        id="edit-<?= $category->category_id ?>"
                                        class="category-edit-form"
                                        style="display:none;"
                                    >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="edit"
                                        >

                                        <input
                                            type="hidden"
                                            name="category_id"
                                            value="<?= encode($category->category_id) ?>"
                                        >

                                        <input
                                            type="text"
                                            name="category_name"
                                            value="<?= encode($category->category_name) ?>"
                                            required
                                        >

                                        <button
                                            type="submit"
                                            class="save-category-btn"
                                        >
                                            Save
                                        </button>

                                        <button
                                            type="button"
                                            class="cancel-category-btn"
                                            onclick="cancelEdit(<?= $category->category_id ?>)"
                                        >
                                            Cancel
                                        </button>

                                    </form>

                                </td>


                                <!-- ACTION -->
                                <td>

                                    <div class="category-actions">

                                        <button
                                            type="button"
                                            class="edit-user-btn"
                                            onclick="editCategory(<?= $category->category_id ?>)"
                                        >
                                            Edit
                                        </button>


                                        <form
                                            method="post"
                                            onsubmit="return confirm('Are you sure you want to delete this category?');"
                                        >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="delete"
                                            >

                                            <input
                                                type="hidden"
                                                name="category_id"
                                                value="<?= encode($category->category_id) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="delete-category-btn"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>


</body>

</html>