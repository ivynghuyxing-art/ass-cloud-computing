<?php

$stm = $_db->query(
    "SELECT *
     FROM user
     ORDER BY user_id DESC"
);

$users = $stm->fetchAll();

?>

<div class="users-page">

    <div class="page-header">
        <h1>User Management</h1>

        <p class="muted">
            View and manage registered users in the library system.
        </p>
    </div>

    <div class="users-card">

        <?php if (empty($users)): ?>

            <p>No users found.</p>

        <?php else: ?>

            <div class="table-wrapper">

                <table class="user-table">

                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($users as $user): ?>

                            <tr>

                                <td>
                                    <?= encode($user->user_id) ?>
                                </td>

                                <td>
                                    <?= encode($user->name) ?>
                                </td>

                                <td>
                                    <?= encode($user->email) ?>
                                </td>

                                <td>
                                    <?= encode($user->gender ?? '-') ?>
                                </td>

                                <td>
                                    <?= encode($user->role ?? '-') ?>
                                </td>

                                <td>
                                    <a
                                        href="admin_panel.php?page=edit_user&id=<?= $user->user_id ?>"
                                        class="edit-user-btn"
                                    >
                                        Edit
                                    </a>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>