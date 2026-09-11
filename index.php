<!DOCTYPE html>
<html lang="th" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ระบบจัดการเงินส่วนกลาง - เข้าสู่ระบบ</title>
  <!-- DaisyUI v5 & Tailwind CSS v4 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-base-200 min-h-screen flex flex-col">

  <div class="navbar bg-base-100 shadow-md px-4">
    <div class="flex-1">
      <a class="btn btn-ghost text-xl font-bold ">
        💰 ระบบจัดการเงินส่วนกลาง
      </a>
    </div>
  </div>

  <div class="flex-1 flex items-center justify-center p-4">
    <div class="card shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
      <form action="./API/login.php" method="post" class="card-body space-y-4">
        <h2 class="card-title text-2xl font-bold justify-center mb-2">เข้าสู่ระบบ</h2>
        
        <fieldset class="fieldset">
          <legend class="fieldset-legend">ชื่อผู้ใช้</legend>
          <label class="input w-full">
            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <rect width="20" height="16" x="2" y="4" rx="2"/>
              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
            <input type="text" class="grow" name="user" placeholder="CPETC" required />
          </label>
        </fieldset>

        <fieldset class="fieldset">
          <legend class="fieldset-legend">รหัสผ่าน</legend>
          <label class="input w-full">
            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <input type="password" class="grow" name="pass" placeholder="••••••••" required />
          </label>
          
        </fieldset>
        <div class="pt-2">
          <button type="submit" class="btn btn-primary w-full">เข้าสู่ระบบ</button>
        </div>
      </form>
    </div>
  </div>
<?php require_once "./API/alarmAndNotify.php" ?>

</body>
</html>