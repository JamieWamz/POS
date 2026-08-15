<?php

require_once __DIR__ . '/../../models/audit.model.php';
$activityRows = ModelAudit::recent(200);
?>
<div class="content-wrapper">
  <section class="content-header">
    <div><span class="pos-section-label">Administration</span><h1>Activity log</h1><p class="pos-page-description">Review sign-ins and changes to sales, stock, users and reports.</p></div>
    <ol class="breadcrumb"><li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li><li class="active">Activity log</li></ol>
  </section>
  <section class="content">
    <div class="box">
      <div class="box-header with-border">
        <div><span class="pos-eyebrow">Administrator controls</span><h3 class="box-title">Recent system activity</h3></div>
        <span class="pos-count-badge"><?php echo count($activityRows); ?></span>
      </div>
      <div class="box-body table-responsive no-padding">
        <table class="table table-striped activityTable">
          <thead><tr><th>Time</th><th>Team member</th><th>Action</th><th>Record</th><th>Details</th><th>IP address</th></tr></thead>
          <tbody>
            <?php if ($activityRows === []): ?><tr><td colspan="6" class="text-center text-muted">Activity will appear here as the team uses the POS.</td></tr><?php endif; ?>
            <?php foreach ($activityRows as $activity): ?>
              <?php
              $metadata = json_decode((string) ($activity['metadata'] ?? ''), true);
              $details = is_array($metadata) ? json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
              ?>
              <tr>
                <td data-order="<?php echo e($activity['created_at']); ?>"><?php echo e(date('j M Y, H:i:s', strtotime((string) $activity['created_at']))); ?></td>
                <td><?php echo e($activity['user_name'] ?? 'System'); ?></td>
                <td><span class="pos-pill"><?php echo e(str_replace('.', ' ', (string) $activity['action'])); ?></span></td>
                <td><?php echo e($activity['entity_type']); ?><?php echo $activity['entity_id'] !== null ? ' #' . e($activity['entity_id']) : ''; ?></td>
                <td class="pos-audit-details"><?php echo e($details ?: '—'); ?></td>
                <td><?php echo e($activity['ip_address'] ?: '—'); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>
<script>$('.activityTable').DataTable({order: [[0, 'desc']], pageLength: 25});</script>
