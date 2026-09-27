<dialog id="insert_user" class="modal modal-bottom sm:modal-middle">
  <div class="modal-box rounded-2xl shadow-2xl border border-base-200 p-6 relative">
    <!-- ปุ่มปิด Modal มุมขวาบน -->
    <form method="dialog">
      <button class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3">✕</button>
    </form>

    <form class="space-y-4" action="<?php echo base_url('/API/admin/process.php'); ?>" method="post">
      <h2 class="text-2xl font-bold text-center text-base-content mb-4">ลงทะเบียนสมาชิกใหม่</h2>
      
      <input type="hidden" name="action" value="insertUser">

      <!-- ชื่อ - นามสกุล -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <fieldset class="fieldset">
          <legend class="fieldset-legend font-medium">ชื่อ</legend>
          <label class="input input-bordered w-full flex items-center gap-2">
            <svg class="h-[1.2em] w-[1.2em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            <input type="text" name="fname" class="grow" placeholder="ชื่อจริง" required />
          </label>
        </fieldset>

        <fieldset class="fieldset">
          <legend class="fieldset-legend font-medium">นามสกุล</legend>
          <label class="input input-bordered w-full flex items-center gap-2">
            <svg class="h-[1.2em] w-[1.2em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
            <input type="text" name="lname" class="grow" placeholder="นามสกุล" required />
          </label>
        </fieldset>
      </div>

      <!-- Username -->
      <fieldset class="fieldset">
        <legend class="fieldset-legend font-medium">ชื่อผู้ใช้ (Username)</legend>
        <label class="input input-bordered w-full flex items-center gap-2">
          <svg class="h-[1.2em] w-[1.2em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="4"/>
            <path d="M16 12v1a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"/>
          </svg>
          <input type="text" name="user" class="grow" placeholder="Username" required />
        </label>
      </fieldset>

      <!-- สิทธิ์การใช้งาน (Role) -->
      <fieldset class="fieldset">
        <legend class="fieldset-legend font-medium">สิทธิ์การใช้งาน</legend>
        <label class="select select-bordered w-full flex items-center gap-2">
          
          </svg>
          <select name="rule" name="rule" class="grow">
            <option value="user" selected>User</option>
            <option value="admin">Admin</option>
          </select>
        </label>
      </fieldset>

      <!-- ปุ่มบันทึกข้อมูล -->
      <div class="modal-action pt-2">
        <button type="submit" class="btn btn-primary w-full shadow-md">บันทึกข้อมูล</button>
      </div>
    </form>
  </div>

  <!-- คลิกพื้นหลังดำเพื่อปิด Modal -->
  <form method="dialog" class="modal-backdrop">
    <button>close</button>
  </form>
</dialog>