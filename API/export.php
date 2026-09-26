<?php 
require_once __DIR__ . '/../vendor/autoload.php';
include_once "../db.php";
include_once "./functions.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
$id = $_GET['id']; //id event
$ad_id = $_SESSION['auth']['admin_group'];
if(empty($id) || empty($ad_id)){
    $_SESSION['alarm'] = "ข้อมูลไม่ครบ";
    backPage();
    exit;
}
echo $id;
$data_event =  protectSelect($conn,"SELECT * FROM `events` WHERE id = :id AND id_group_admin = :ad_id",["id" => $id,"ad_id"=>$ad_id],0);

if(empty($data_event)){
    $_SESSION['alarm'] = "ไม่พบข้อมูล";
    backPage();
    exit;
}

$project_title = $data_event['title']; // ชื่อกิจกรรม/หัวข้อ
$amount_per_person = $data_event['details'];       // รายละเอียดราคา

// =========================================================================
// [จุดที่ 2] ข้อมูลรายชื่อสมาชิก (Data List)
// =========================================================================
// !!! แก้ไขตัวแปร $members_data ให้ดึงมาจาก PDO/MySQL Query ของคุณ !!!
// ตัวอย่างรูปแบบ Array: [['first_name' => '...', 'last_name' => '...', 'status' => '...'], ...]
// $members_data = [
//     ['first_name' => 'สิริพร', 'last_name' => 'งดงาม', 'status' => 'ผ่าน'],[cite: 1]
//     ['first_name' => 'กิตติ', 'last_name' => 'รักเรียน', 'status' => 'ผ่าน'],[cite: 1]
//     ['first_name' => 'อนุชา', 'last_name' => 'ตั้งใจ', 'status' => 'ผ่าน'],[cite: 1]
//     ['first_name' => 'วิภา', 'last_name' => 'ผู้ช่วย', 'status' => 'ไม่ผ่าน'],[cite: 1]
//     ['first_name' => 'วรัญชัย', 'last_name' => 'วิใจคำ', 'status' => 'ผ่าน'],[cite: 1]
//     ['first_name' => 'ธีรภัทร์', 'last_name' => 'กล้าหาญ', 'status' => 'ผ่าน'],[cite: 1]
// ];

// =========================================================================
// เริ่มสร้าง Excel
// =========================================================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('รายชื่อผู้ชำระเงิน');

// 1. ใส่ชื่อหัวข้อหลัก และรายละเอียด
$sheet->setCellValue('A1', $project_title);
$sheet->mergeCells('A1:D1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

$sheet->setCellValue('A2', $amount_per_person);
$sheet->mergeCells('A2:D2');
$sheet->getStyle('A2')->getFont()->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));

// 2. สร้าง Header ของตาราง (บรรทัดที่ 4)
$headers = ['ลำดับ', 'ชื่อ', 'นามสกุล', 'สถานะ'];
$columns = ['A', 'B', 'C', 'D'];

foreach ($headers as $index => $header) {
    $col = $columns[$index];
    $sheet->setCellValue($col . '4', $header);
}

// ตกแต่ง Header ตาราง
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4F46E5'] // สีน้ำเงินอมม่วง
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];
$sheet->getStyle('A4:D4')->applyFromArray($headerStyle);

// 3. วนลูปใส่ข้อมูลสมาชิก (เริ่มบรรทัดที่ 5)
$row = 5;
$no = 1;
$sql = 'SELECT 
    m.id, 
    m.fname, 
    m.lname, 
    s.status, 
    s.file_name, 
    s.date, 
    s.id AS slip_id
FROM `mem_event` AS e 
JOIN `members` AS m ON e.id_mem = m.id 
LEFT JOIN `slips` AS s ON m.id = s.add_by AND s.id_event = e.id_event
WHERE e.id_event = :id_event AND m.is_deleted = 0 
LIMIT 0, 25;';
$members_data = protectSelect($conn,$sql,['id_event'=>$id],1);

foreach ($members_data as $item) {
    $sheet->setCellValue('A' . $row, $no);
    $sheet->setCellValue('B' . $row, $item['fname']);
    $sheet->setCellValue('C' . $row, $item['lname']);
    $sheet->setCellValue('D' . $row, $item['status']);

    // จัดตำแหน่งลำดับให้อยู่ตรงกลาง
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // ตกแต่งสีตัวอักษรสถานะ (ผ่าน = เขียว, ไม่ผ่าน = แดง)
    if ($item['status'] === 'ผ่าน') {
        $sheet->getStyle('D' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('16A34A')); // สีเขียว
    } else {
        $sheet->getStyle('D' . $row)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626')); // สีแดง
    }

    $row++;
    $no++;
}

// 4. ใส่เส้นขอบตาราง (Border)
$borderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'E5E7EB'],
        ],
    ],
];
$sheet->getStyle('A4:D' . ($row - 1))->applyFromArray($borderStyle);

// 5. ปรับความกว้างคอลัมน์อัตโนมัติ
foreach ($columns as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// =========================================================================
// [จุดที่ 3] การส่งออกไฟล์ Excel ให้ดาวน์โหลดทาง Web Browser
// =========================================================================
// !!! แก้ไขชื่อไฟล์ตามต้องการ !!!
if (ob_get_length()) {
    ob_end_clean();
}

// 5. การส่งออกไฟล์ Excel ให้ดาวน์โหลดทาง Web Browser
$filename = $data_event['title'] . date('Y-m-d') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . urlencode($filename) . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;