<?php require_once "../TEMPLATES/user/head_user.php"; ?>
<?php require_once "../TEMPLATES/user/nav_user.php"; ?>
<?php include_once "../../db.php"; ?>
<?php require_once "../../API/auth.php"; ?>

<?php 
$sql = "SELECT
            s.status, s.date, e.title, e.details, e.id
        FROM
            `mem_event` AS me
        JOIN `events` AS e
            ON e.id = me.id_event
        LEFT JOIN `slips` AS s
            ON s.add_by = me.id_mem AND s.id_event = e.id
        WHERE
            me.id_mem = :id;";

$count = 1; 
$data_event = protectSelect($conn, $sql, ['id' =>$_SESSION['auth']['id']], 1) ?: [];
?>

<div class="container mx-auto px-4 my-8">
    <div class="bg-base-100 p-6 rounded-2xl shadow-xl border border-base-200">
        <!-- Header -->
        <div class="pb-4 mb-6 border-b border-base-200">
            <h2 class="text-2xl font-bold tracking-tight text-base-content">รายการกิจกรรมของคุณ</h2>
            <p class="text-sm text-base-content/60">ตรวจสอบสถานะการแนบสลิปชำระเงินและรายละเอียดกิจกรรม</p>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto rounded-xl border border-base-200">
            <table class="table table-zebra w-full">
                <!-- Head -->
                <thead class="bg-base-200/60 text-base-content/80">
                    <tr>
                        <th class="w-16 text-center">ลำดับ</th>
                        <th>ชื่อรายการ</th>
                        <th>รายละเอียด</th>
                        <th class="text-center w-36">สถานะ</th>
                    </tr>
                </thead>
                <!-- Body -->
                <tbody>
                    <?php if (!empty($data_event)) { 
                        foreach ($data_event as$value) {
                            // จัดการ Badge ตามสถานะ
                            $badge = match ($value['status'] ?? '') {
                                'ผ่าน' => '<div class="badge badge-ml badge-soft badge-success gap-1">ผ่าน</div>',
                                'รอตรวจสอบ' => '<div class="badge badge-ml badge-soft badge-warning gap-1">รอตรวจสอบ</div>',
                                'ไม่ผ่าน' => '<div class="badge badge-ml badge-soft badge-error gap-1">ไม่ผ่าน</div>',
                                default => '<div class="badge badge-ml badge-soft badge-error gap-1">ยังไม่ได้แนบสลิป</div>',
                            };
                    ?>
                        <tr onclick="window.location.href='<?= base_url('VIEW/USER/detail.php?id=') . urlencode($value['id']) ?>'" 
                            class="hover:bg-base-200/70 transition-colors cursor-pointer">
                            <th class="text-center font-normal text-base-content/70"><?= $count++; ?></th>
                            <td class="font-medium text-base-content"><?= htmlspecialchars($value['title'] ?? ''); ?></td>
                            <td class="text-base-content/80 max-w-xs truncate"><?= htmlspecialchars($value['details'] ?? ''); ?></td>
                            <td class="text-center text-sm"><?= $badge; ?></td>
                        </tr>
                    <?php 
                        }
                    } else { 
                    ?>
                        <tr>
                            <td colspan="4" class="text-center py-10 text-base-content/50">
                                ไม่พบรายการกิจกรรมของคุณในขณะนี้
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once "../TEMPLATES/user/footer.php"; ?>