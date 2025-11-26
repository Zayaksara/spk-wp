<?php
$page_title = 'Kelola Analisis WP';
require_once __DIR__ . '/layouts/header.php';
?>
<div class="manage-container">
    <div class="page-header">
        <h1>Kelola Analisis WP</h1>
        <div style="display: flex; gap: 10px;">
            <a href="<?= BASE_URL ?>?action=dashboard" class="btn-new" style="background: #4CAF50;">📊 Dashboard</a>
            <a href="<?= BASE_URL ?>?action=create" class="btn-new">+ Analisis Baru</a>
        </div>
    </div>
    
    <div class="table-container">
        <?php if (count($analisis_list) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Analisis</th>
                    <th>Metode</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($analisis_list as $index => $analisis): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($analisis['judul']) ?></td>
                    <td><?= htmlspecialchars($analisis['metode']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($analisis['created_at'])) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?= BASE_URL ?>?action=edit&id=<?= $analisis['id'] ?>" class="btn-edit">Edit</a>
                            <a href="<?= BASE_URL ?>?action=view&id=<?= $analisis['id'] ?>" class="btn-view">Lihat</a>
                            <button class="btn-delete" onclick="deleteAnalisis(<?= $analisis['id'] ?>)">Hapus</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <p>Belum ada analisis. <a href="<?= BASE_URL ?>?action=create">Buat analisis baru</a></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function deleteAnalisis(id) {
        if (confirm('Apakah Anda yakin ingin menghapus analisis ini?')) {
            fetch('<?= BASE_URL ?>?action=delete&id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus analisis');
                }
            });
        }
    }
</script>
<?php require_once __DIR__ . '/layouts/footer.php'; ?>

