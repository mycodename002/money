<body>
    <div class="navbar bg-base-100 shadow-sm">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl">💰 ระบบจัดการเงินส่วนกลาง</a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
                <li><a>Link</a></li>
                <?php ?>
                    <li>
                        <details>
                            <summary>จัดการสมาชิก</summary>
                            <ul class="bg-base-100 rounded-t-none p-2">
                                <li onclick="insert_user.showModal()"><a >เพิ่มสมาชิก</a></li>
                                <li><a>Link 2</a></li>
                            </ul>
                        </details>
                    </li>
                <?php ?>
            </ul>
        </div>
    </div>