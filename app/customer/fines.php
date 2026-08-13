<?php
require_once '../_base.php';
$title = 'My Fines';
$_title = '';
include 'customer_header.php';

if (!$_user) {
    redirect('/login.php');
}

$user_id = $_user->user_id;

// Get all fines with borrowing and book info
$stm = $_db->prepare("
    SELECT 
        f.fine_id,
        f.amount,
        f.reason,
        f.payment_status,
        f.borrowing_id,
        bk.book_id,
        bk.title,
        bk.author,
        bk.book_photo
    FROM fine f
    JOIN borrowing b ON f.borrowing_id = b.borrowing_id
    JOIN book bk ON b.book_id = bk.book_id
    WHERE b.user_id = ?
    ORDER BY f.fine_id DESC
");
$stm->execute([$user_id]);
$fines = $stm->fetchAll();

// Calculate totals
$total_fines = 0;
$total_unpaid = 0;
$total_paid = 0;

foreach ($fines as $fine) {
    $total_fines += $fine->amount;
    if ($fine->payment_status === 'unpaid') {
        $total_unpaid += $fine->amount;
    } else {
        $total_paid += $fine->amount;
    }
}

// Handle payment (mark as paid)
if (is_post() && post('action') === 'pay') {
    $fine_id = post('fine_id');
    $stm = $_db->prepare("UPDATE fine SET payment_status = 'paid' WHERE fine_id = ?");
    $stm->execute([$fine_id]);
    temp('info', 'Fine paid successfully!');
    redirect('/customer/fines.php');
}
?>

<main class="fines-page">
    
    <!-- ===== Page Header ===== -->
    <div class="page-header">
        <h1>💰 My Fines</h1>
        <p>View and manage your library fines</p>
    </div>

    <!-- ===== Stats ===== -->
    <div class="fines-stats">
        <div class="stat-card">
            <span class="stat-number">RM <?= number_format($total_fines, 2) ?></span>
            <span class="stat-label">Total Fines</span>
        </div>
        <div class="stat-card <?= $total_unpaid > 0 ? 'stat-danger' : '' ?>">
            <span class="stat-number">RM <?= number_format($total_unpaid, 2) ?></span>
            <span class="stat-label">Unpaid</span>
        </div>
        <div class="stat-card stat-success">
            <span class="stat-number">RM <?= number_format($total_paid, 2) ?></span>
            <span class="stat-label">Paid</span>
        </div>
    </div>

    <!-- ===== Fines Table ===== -->
    <div class="table-container">
        <?php if (empty($fines)): ?>
            <div class="empty-state" style="text-align:center;">
                <span class="empty-icon">🎉</span>
                <h2>No fines found!</h2>
                <p>Keep up the good work returning books on time.</p>
            </div>
        <?php else: ?>
            <table class="fines-table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Reason</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fines as $fine): ?>
                        <tr>
                            <td>
                                <div class="book-info">
                                    <img src="/admin/book_photo/<?= encode($fine->book_photo ?: 'default.png') ?>" 
                                         alt="<?= encode($fine->title) ?>">
                                    <div>
                                        <div class="book-title"><?= encode($fine->title) ?></div>
                                        <div class="book-author">by <?= encode($fine->author) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= encode($fine->reason ?: 'Overdue fine') ?></td>
                            <td class="amount">RM <?= number_format($fine->amount, 2) ?></td>
                            <td>
                                <?php if ($fine->payment_status === 'paid'): ?>
                                    <span class="badge-paid">✅ Paid</span>
                                <?php elseif ($fine->payment_status === 'pending'): ?>
                                    <span class="badge-pending">⏳ Pending</span>
                                <?php else: ?>
                                    <span class="badge-unpaid">⚠️ Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($fine->payment_status !== 'paid'): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="pay">
                                        <input type="hidden" name="fine_id" value="<?= $fine->fine_id ?>">
                                        <button type="submit" class="btn-pay" 
                                                onclick="return confirm('Pay RM <?= number_format($fine->amount, 2) ?> for this fine?')">
                                            Pay Now
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
        <?php endif; ?>
    </div>

</main>


<?php include '../footer.php'; ?>