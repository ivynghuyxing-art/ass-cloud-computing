<?php
require_once '../_base.php';
$title = 'My Borrowing History';
$_title = '';
include 'customer_header.php';

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
        bk.photo
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
            <h1>Borrowing History</h1>
    <p>A record of every book you've borrowed from Book Nest.</p>
  </div>

  <div class="table-card">
    <?php if (empty($records)): ?>
      <div class="empty-msg" style="text-align:center;">You haven't borrowed any books yet.</div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Book</th>
            <th>Borrowed On</th>
            <th>Due Date</th>
            <th>Returned On</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($records as $r): ?>
            <?php
              $status = $r['status'];
              $badgeClass = $status === 'overdue' ? 'badge-overdue' : ($status === 'returned' ? 'badge-returned' : 'badge-active');
              $statusLabel = ucfirst($status);
            ?>
            <tr>
              <td>
                <div class="book-title"><?= htmlspecialchars($r['title']) ?></div>
                <div class="book-author"><?= htmlspecialchars($r['author']) ?></div>
              </td>
              <td><?= htmlspecialchars($r['borrow_date']) ?></td>
              <td><?= htmlspecialchars($r['due_date']) ?></td>
              <td><?= $r['return_date'] ? htmlspecialchars($r['return_date']) : '—' ?></td>
              <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</main>

<?php include '../footer.php'; ?>