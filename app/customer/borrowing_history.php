<?php require_once '../_base.php';
$title = 'My Borrowing History';
$_title = '';
include 'customer_header.php';

if (!$_user) {
    redirect('/login.php');
}

$user_id = $_user->user_id;

$stm = $_db->prepare("
    SELECT 
        b.borrowing_id,
        b.borrow_date,
        b.due_date,
        b.return_date,
        b.status,
        bk.book_id,
        bk.title,
        bk.author,
        bk.book_photo
    FROM borrowing b
    JOIN book bk ON b.book_id = bk.book_id
    WHERE b.user_id = ?
    ORDER BY b.borrow_date DESC
");
$stm->execute([$user_id]);
$records = $stm->fetchAll();
?>

<main class="borrowing-page">
    
    <!-- ===== Page Header ===== -->
    <div class="page-header">
        <h1>📚 Borrowing History</h1>
        <p>A record of every book you've borrowed from Book Nest</p>
    </div>

    <!-- ===== BORROWING TABLE ===== -->
    <?php if (empty($records)): ?>
        <div class="empty-state" style="text-align:center; padding:60px 20px;">
            <span style="font-size:48px;">📖</span>
            <h2>No borrowing records yet</h2>
            <p>Start browsing and borrowing books from our collection!</p>
            <a href="/customer/catalog.php" class="btn-browse">Browse Books →</a>
        </div>
    <?php else: ?>
        <div class="borrowing-table-container">
            <table class="borrowing-table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Borrowed On</th>
                        <th>Due Date</th>
                        <th>Returned On</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <?php
                        // Convert timestamps to dates
                        $borrow_date = date('Y-m-d', $record->borrow_date);
                        $due_date = date('Y-m-d', $record->due_date);
                        $return_date = $record->return_date ? date('Y-m-d', $record->return_date) : '—';
                        
                        // Determine status badge
                        $status = $record->status;
                        if ($status === 'returned') {
                            $badge_class = 'badge-returned';
                            $badge_text = '✓ Returned';
                        } elseif ($status === 'overdue') {
                            $badge_class = 'badge-overdue';
                            $badge_text = '⚠ Overdue';
                        } else {
                            $badge_class = 'badge-borrowed';
                            $badge_text = '📖 Borrowed';
                        }
                        ?>
                        <tr>
                            <td>
                                <div class="borrowing-book-info">
                                    <img src="/admin/book_photo/<?= encode($record->book_photo ?: 'default.png') ?>" 
                                         alt="<?= encode($record->title) ?>"
                                         class="borrowing-book-thumb">
                                    <div class="borrowing-book-details">
                                        <div class="borrowing-title">
                                            <?= encode($record->title) ?>
                                        </div>
                                        <div class="borrowing-author">
                                            by <?= encode($record->author) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($borrow_date) ?></td>
                            <td><?= htmlspecialchars($due_date) ?></td>
                            <td><?= htmlspecialchars($return_date) ?></td>
                            <td>
                                <span class="badge <?= $badge_class ?>">
                                    <?= $badge_text ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($status !== 'returned'): ?>
                                    <form method="post" action="return_book.php" style="display:inline;">
                                        <input type="hidden" name="borrowing_id" value="<?= $record->borrowing_id ?>">
                                        <button type="submit" class="btn-return">
                                            Return
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>

<?php include '../footer.php'; ?>