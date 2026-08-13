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

<div class="container">


    <!-- ==============================
         HEADER
    =============================== -->

    <div class="page-header">

        <h2>Category Management</h2>

        <a href="admin_panel.php?page=add_category"
            class="add-button"
        >
            + Add Category
        </a>

    </div>

    <!-- ==============================
         CATEGORY TABLE
    =============================== -->

    <table>

        <thead>

            <tr>

                <th>ID</th>

                <th>Category Name</th>

                <th>Action</th>

            </tr>

        </thead>


        <tbody>

        <?php if ($categories): ?>

            <?php foreach ($categories as $category): ?>

                <tr>

                    <td>
                        <?= encode($category->category_id) ?>
                    </td>


                    <td>

                        <!-- Normal text -->

                        <span
                            id="name-<?= $category->category_id ?>"
                        >
                            <?= encode($category->category_name) ?>
                        </span>


                        <!-- Edit form -->

                        <form
                            method="post"
                            id="edit-<?= $category->category_id ?>"
                            class="edit-form"
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
                                class="save-button"
                            >
                                Save
                            </button>

                            <button
                                type="button"
                                class="cancel-button"
                                onclick="cancelEdit(<?= $category->category_id ?>)"
                            >
                                Cancel
                            </button>

                        </form>

                    </td>


                    <td>

                        <!-- EDIT -->

                        <button
                            type="button"
                            class="edit-button"
                            onclick="editCategory(<?= $category->category_id ?>)"
                        >
                            Edit
                        </button>


                        <!-- DELETE -->

                        <form
                            method="post"
                            style="display:inline;"
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
                                class="delete-button"
                            >
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="3">
                    No categories found.
                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>


</body>

</html>