<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// English language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-20
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'windows-874';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'ตัด'
  ),
  'copy' => array(
    'title' => 'คัดลอก'
  ),
  'paste' => array(
    'title' => 'วาง'
  ),
  'undo' => array(
    'title' => 'เลิกทำ'
  ),
  'redo' => array(
    'title' => 'ทำซ้ำ'
  ),
  'image_insert' => array(
    'title' => 'แทรกรูป',
    'select' => 'เลือก',
	'delete' => 'ลบ', // new 1.0.5
    'cancel' => 'ยกเลิก',
    'library' => 'ไลบราลี่',
    'preview' => 'แสดงตัวอย่าง',
    'images' => 'รูปภาพ',
    'upload' => 'อัพโหลดภาพ',
    'upload_button' => 'อัพโหลด',
    'error' => 'ผิดพลาด!',
    'error_no_image' => 'โปรดทำการเลือกภาพ',
    'error_uploading' => 'การความผิดพลาดขึ้นระหว่างทำการอัพโหลดไฟล์. ลองใหม่อีกครั้ง',
    'error_wrong_type' => 'รูปแบบไฟล์ผิด',
    'error_no_dir' => 'ไม่พบไลบรารี'
	'error_cant_delete' => 'การลบล้มเหลว', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'คุณสมบัติรูปภาพ',
    'ok' => '   ตกลง   ',
    'cancel' => 'ยกเลิก',
    'source' => 'แหล่งข้อมูล',
    'alt' => 'ข้อความทางเลือก',
    'align' => 'การจัดตำแหน่งข้อความ',
    'left' => 'ชิดซ้าย',
    'right' => 'ชิดขวา',
    'top' => 'ชิดบนสุด',
    'middle' => 'กึ่งกลาง',
    'bottom' => 'ชิดล่าง',
    'absmiddle' => 'อยู่กึ่งกลางพอดี',
    'texttop' => 'texttop',
    'baseline' => 'baseline',
    'width' => 'ความกว้าง',
    'height' => 'ความสูง',
    'border' => 'ความหนาขอบ',
    'hspace' => 'ช่องว่างแนวนอน',
    'vspace' => 'ช่องว่างแนวตั้ง',
    'error' => 'ผิดพลาด!',
    'error_width_nan' => 'ความกว้างที่ใส่ไม่ใช่ค่าตัวเลข',
    'error_height_nan' => 'ความสูงที่ใส่ไม่ใช่ค่าตัวเลข',
    'error_border_nan' => 'ความหนาขอบที่ใส่ไม่ใช่ค่าตัวเลข',
    'error_hspace_nan' => 'ช่องว่างแนวนอนที่ใส่ไม่ใชค่าตัวเลข',
    'error_vspace_nan' => 'ช่องว่างแนวตั้งที่ใส่ไม่ใชค่าตัวเลข',
  ),
  'hr' => array(
    'title' => 'เส้นแบ่งบรรทัด'
  ),
  'table_create' => array(
    'title' => 'สร้างตาราง'
  ),
  'table_prop' => array(
    'title' => 'คุณสมบัติตาราง',
    'ok' => '   ตกลง   ',
    'cancel' => 'ยกเลิก',
    'rows' => 'แถว',
    'columns' => 'คอลัมภ์',
    'css_class' => 'CSS คลาส', // <=== new 1.0.6
    'width' => 'ความกว้าง',
    'height' => 'ความสูง',
    'border' => 'ความหนาขอบ',
    'pixels' => 'พิกเซล',
    'cellpadding' => 'Cell padding',
    'cellspacing' => 'Cell spacing',
    'bg_color' => 'สีพื้นหลัง',
    'background' => 'ภาพพื้นหลัง', // <=== new 1.0.6
    'error' => 'ผิดพลาด!',
    'error_rows_nan' => 'ค่าแถวที่ใส่ไม่ใชค่าตัวเลข',
    'error_columns_nan' => 'ค่าคอลัมภ์ที่ใส่ไม่ใชค่าตัวเลข',
    'error_width_nan' => 'ค่าความกว้างที่ใส่ไม่ใชค่าตัวเลข',
    'error_height_nan' => 'ค่าความสูงที่ใส่ไม่ใชค่าตัวเลข',
    'error_border_nan' => 'ค่าความหนาขอบที่ใส่ไม่ใชค่าตัวเลข',
    'error_cellpadding_nan' => 'Cell padding ที่ใส่ไม่ใชค่าตัวเลข',
    'error_cellspacing_nan' => 'Cell spacing ที่ใส่ไม่ใชค่าตัวเลข',
  ),
  'table_cell_prop' => array(
    'title' => 'คุณสมบัติเซลล์',
    'horizontal_align' => 'การจัดแนวนอน',
    'vertical_align' => 'การจัดแนวตึ้ง',
    'width' => 'ความกว้าง',
    'height' => 'ความสูง',
    'css_class' => 'CSS คลาส',
    'no_wrap' => 'ไม่ล้อมข้อความ',
    'bg_color' => 'สีพื้นหลัง',
    'background' => 'ภาพพื้นหลัง', // <=== new 1.0.6
    'ok' => '   ตกลง   ',
    'cancel' => 'ยกเลิก',
    'left' => 'ชิดซ้าย',
    'center' => 'จัดกลาง',
    'right' => 'ชิดขวา',
    'top' => 'ชิดบน',
    'middle' => 'จัดกลาง',
    'bottom' => 'ชิดล่าง',
    'baseline' => 'Baseline',
    'error' => 'ผิดพลาด!',
    'error_width_nan' => 'ค่าความกว้างที่ใส่ไม่ใชค่าตัวเลข',
    'error_height_nan' => 'ค่าความสูงที่ใส่ไม่ใชค่าตัวเลข',
  ),
  'table_row_insert' => array(
    'title' => 'แทรกแถว'
  ),
  'table_column_insert' => array(
    'title' => 'แทรกคอลัมภ์'
  ),
  'table_row_delete' => array(
    'title' => 'ลบแถว'
  ),
  'table_column_delete' => array(
    'title' => 'ลบคอลัมภ์'
  ),
  'table_cell_merge_right' => array(
    'title' => 'รวมเซลล์ทางด้านขวา'
  ),
  'table_cell_merge_down' => array(
    'title' => 'รวมเซลล์ด้านซ้าย'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'แบ่งเซลล์ทางแนวนอน'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'แบ่งเซลล์ทางแนวตั้ง'
  ),
  'style' => array(
    'title' => 'สไตล์'
  ),
  'font' => array(
    'title' => 'รูปแบบอักษร'
  ),
  'fontsize' => array(
    'title' => 'ขนาด'
  ),
  'paragraph' => array(
    'title' => 'ย่อหน้า'
  ),
  'bold' => array(
    'title' => 'ตัวหนา'
  ),
  'italic' => array(
    'title' => 'ตัวเอียง'
  ),
  'underline' => array(
    'title' => 'ขีดเส้นใต้'
  ),
  'ordered_list' => array(
    'title' => 'สัญลักษณ์หัวข้อย่อยเรียงลำด้บ'
  ),
  'bulleted_list' => array(
    'title' => 'สัญลักษณ์หัวข้อย่อย'
  ),
  'indent' => array(
    'title' => 'อินเด็นท์'
  ),
  'unindent' => array(
    'title' => 'อันอินเด็นท์'
  ),
  'left' => array(
    'title' => 'ซ้าย'
  ),
  'center' => array(
    'title' => 'กลาง'
  ),
  'right' => array(
    'title' => 'ขวา'
  ),
  'fore_color' => array(
    'title' => 'สีพื้นหน้า'
  ),
  'bg_color' => array(
    'title' => 'สีพื้นหลัง'
  ),
  'design_tab' => array(
    'title' => 'สลับสู่โหมด WYSIWYG (ออกแบบ) '
  ),
  'html_tab' => array(
    'title' => 'สลับสู่โหมด HTML (โค้ด) '
  ),
  'colorpicker' => array(
    'title' => 'จานสี',
    'ok' => '   ตกลง   ',
    'cancel' => 'ยกเลิก',
  ),
  'cleanup' => array(
    'title' => 'HTML cleanup (ยกเลิกสไตล์)',
    'confirm' => 'การกระทำนี้เป็นการยกเลิการใช้งานสไตล์, รูปแบบอักษรและคำสั่งบางอันที่มีผลกับบทความนี้จะหายไป.',
    'ok' => '   ตกลง  ',
    'cancel' => 'ยกเลิก',
  ),
  'toggle_borders' => array(
    'title' => 'สลับความหนาขอบ',
  ),
  'hyperlink' => array(
    'title' => 'ไฮเปอร์ลิงค์',
    'url' => 'URL',
    'name' => 'ชื่อ',
    'target' => 'เป้าหมาย',
    'title_attr' => 'หัวเรื่อง',
	'a_type' => 'รูปแบบ', // <=== new 1.0.6
	'type_link' => 'ลิงค์', // <=== new 1.0.6
	'type_anchor' => 'อังเคอร์r', // <=== new 1.0.6
	'type_link2anchor' => 'ลิงค์ไปยังอังเคอร์', // <=== new 1.0.6
	'anchors' => 'อังเคอร์', // <=== new 1.0.6
    'ok' => '   ตกลง   ',
    'cancel' => 'ยกเลิก',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'เฟรมเดียวกัน (_self)',
	'_blank' => 'หน้าเปล่า (_blank)',
	'_top' => 'เฟรมบนสุด (_top)',
	'_parent' => 'เฟรมหลัก (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'คุณสมบัติแถว',
    'horizontal_align' => 'จัดเรียงตามแนวนอน',
    'vertical_align' => 'จัดเรียงตามแนวตั้ง',
    'css_class' => 'CSS คลาส',
    'no_wrap' => 'ไม่ล้อมข้อความ',
    'bg_color' => 'สีพื้นหลัง',
    'ok' => '   ตกลง  ',
    'cancel' => 'ยกเลิก',
    'left' => 'ชิดซ้าย',
    'center' => 'กึ่งกลาง',
    'right' => 'ชิดขวา',
    'top' => 'ชิดบน',
    'middle' => 'กึ่งกลาง',
    'bottom' => 'ชิดล่าง',
    'baseline' => 'Baseline',
  ),
  'symbols' => array(
    'title' => 'อักขระพิเศษ',
    'ok' => '   ตกลง   ',
    'cancel' => 'ยกเลิก',
  ),
  'templates' => array(
    'title' => 'เทมเพลต',
  ),
  'page_prop' => array(
    'title' => 'คุณสมบัติ',
    'title_tag' => 'หัวเรื่อง',
    'charset' => 'Charset',
    'background' => 'ภาพพื้นหลัง',
    'bgcolor' => 'สีพื้นหลัง',
    'text' => 'สีข้อความ',
    'link' => 'สีลิงค์'
    'vlink' => 'สีลิงค์เยี่ยม',
    'alink' => 'สีลิงค์แอคทิฟ',
    'leftmargin' => 'ระยะขอบซ้าย',
    'topmargin' => 'ระยะขอบบน',
    'css_class' => 'CSS คลาส',
    'ok' => '   ตกลง  ',
    'cancel' => 'ยกเลิก',
  ),
  'preview' => array(
    'title' => 'แสดงตัวอย่าง',
  ),
  'image_popup' => array(
    'title' => 'ป๊อบอัพรูป',
  ),
  'zoom' => array(
    'title' => 'ขยาย',
  ),
);
?>