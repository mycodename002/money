<?php require_once "../TEMPLATES/user/head_user.php"; ?>
<?php require_once "../TEMPLATES/user/nav_user.php";?>
<?php include_once "../../db.php" ?>
<?php require_once "../../API/auth.php"; ?>
<?php $sql = "SELECT
     s.status, s.date, e.title, e.details, e.id
FROM
    `mem_event` AS me
JOIN `events` AS e
ON
    e.id = me.id_event
LEFT JOIN `slips` AS s
ON
    s.add_by = me.id_mem AND s.id_event = e.id
WHERE
    me.id_mem = :id;" ?>

<?php $count =1; $data_event= protectSelect($conn, $sql,['id'=>$_SESSION['auth']['id']], 1);?>
<div class="container mx-auto px-4 mt-4 shadow-sm">
  <div class="overflow-x-auto">
  <table class="table">
    <!-- head -->
    <thead>
      <tr>
        <th></th>
        <th>ชื่อรายการ</th>
        <th>รายละเอียด</th>
        <th>สถานะ</th>
      </tr>
    </thead>
    <tbody>
      <!-- row 1 -->
       <?php foreach ($data_event as $value) {?>
       <?php $badge = ""; if(empty($value['status'])){$badge = '<div class="badge badge-soft badge-error">ยังไม่ได้แนบสลิป</div>';}
       else if($value['status'] == 'ไม่ผ่าน'){$badge = '<div class="badge badge-soft badge-error">ไม่ผ่าน</div>';}
       else if($value['status'] == 'รอตรวจสอบ'){$badge = '<div class="badge badge-soft badge-warning">รอตรวจสอบ</div>';}
       else if($value['status'] == 'ผ่าน'){$badge = '<div class="badge badge-soft badge-success">ผ่าน</div>';} ?>
      
      <tr onclick="window.location.href='<?= base_url('VIEW/USER/detail.php?id=').$value['id']?>'">
        <th><?= $count++; ?> </th>
        <td><?= $value['title'] ?></td>
        <td><?= $value['details'] ?> </td>
        <td><?=  $badge ?></td>
      </tr>
       <?php } ?>
    </tbody>
  </table>
</div>
</div>

<?php require_once "../TEMPLATES/user/footer.php";?>