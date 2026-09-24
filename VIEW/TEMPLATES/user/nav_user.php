<?php require_once '../../API/functions.php' ?>
<?php $href = "";
                    $summary = ""; 
                    if($_SESSION['auth']['rule'] == 'admin'){ 
                        $summary = "จัดการสมาชิก";
                        $href = "ADMIN";
                    } elseif($_SESSION['auth']['rule'] == 'user'){
                        $summary = "ตั้งค่า";
                        $href = "USER";

                    } 
                ?>
<body>
    <div class="navbar bg-base-100 shadow-sm">
        <div class="flex-1">
            <a href="<?php echo base_url("VIEW/".$href); ?>" class="btn btn-ghost text-xl">💰 ระบบจัดการเงินส่วนกลาง</a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
                <li><a>Link</a></li>
                
                <li class="relative">
                    <details>
                        <summary><?= $summary ?></summary>
                        
                        <ul class="bg-base-100 rounded-box p-2 shadow-lg absolute right-0 left-auto top-full mt-2 w-max min-w-[12rem] z-50">
                            <?php if($_SESSION['auth']['rule'] == 'admin'){ ?>
                                <li><a href="<?php echo base_url('/VIEW/ADMIN/showMember.php'); ?>">จัดการสมาชิก</a></li>
                                <li><a href="<?php echo base_url('/VIEW/ADMIN/showEvent.php'); ?>">จัดการรายการ</a></li>
                            <?php } ?>
                            
                            <?php if($_SESSION['auth']['rule'] == 'user'){ ?>
                                <li><a href="<?php echo base_url('/VIEW/USER/changePassword.php'); ?>">เปลี่ยนรหัสผ่าน</a></li>
                            <?php } ?>
                            
                            <li><a href="<?php echo base_url('API/logout.php'); ?>" class="text-error">ออกจากระบบ</a></li>
                        </ul>
                    </details>
                </li>
            </ul>
        </div>
    </div>