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
$spaw_lang_charset = 'euc-kr';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => '잘라내기'
  ),
  'copy' => array(
    'title' => '복사하기'
  ),
  'paste' => array(
    'title' => '붙여넣기'
  ),
  'undo' => array(
    'title' => '실행취소'
  ),
  'redo' => array(
    'title' => '재실행'
  ),
  'image_insert' => array(
    'title' => '이미지 삽입',
    'select' => '선택',
	'delete' => '삭제', // new 1.0.5
    'cancel' => '취소',
    'library' => '라이브러리',
    'preview' => '미리보기',
    'images' => '이미지',
    'upload' => '업로드 이미지',
    'upload_button' => '업로드',
    'error' => '에러',
    'error_no_image' => '이미지를 선택해 주십시오',
    'error_uploading' => '파일 업로드중 에러가 발생하였습니다. 잠시 후 다시 시도해 주십시오',
    'error_wrong_type' => '잘못된 이미지 형식입니다.',
    'error_no_dir' => '라이브러리가 존재하지 않습니다.',
	'error_cant_delete' => '삭제 실패', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => '이미지 속성,
    'ok' => '   확인   ',
    'cancel' => '취소',
    'source' => '소스',
    'alt' => '그림 설명',
    'align' => '정렬',
    'left' => '왼쪽',
    'right' => '오른쪽',
    'top' => '상단',
    'middle' => '중단',
    'bottom' => '하단',
    'absmiddle' => 'absmiddle',
    'texttop' => 'texttop',
    'baseline' => '밑줄',
    'width' => '넓이',
    'height' => '높이',
    'border' => '선 두께',
    'hspace' => 'Hor. space',
    'vspace' => 'Vert. space',
    'error' => '에러',
    'error_width_nan' => '넓이는 숫자로 입력하셔야 합니다. ',
    'error_height_nan' => '높이는 숫자로 입력하셔야 합니다 .',
    'error_border_nan' => '선 두께는 숫자로 입력하셔야 합니다.',
    'error_hspace_nan' => '좌우측 여백',
    'error_vspace_nan' => '상하 여백',
  ),
  'hr' => array(
    'title' => '구분선'
  ),
  'table_create' => array(
    'title' => '표 만들기'
  ),
  'table_prop' => array(
    'title' => '표 속성',
    'ok' => '   확인   ',
    'cancel' => '취소',
    'rows' => '행',
    'columns' => '열',
    'css_class' => 'CSS 클래스', // <=== new 1.0.6
    'width' => '넓이',
    'height' => '높이',
    'border' => '선 두께',
    'pixels' => '픽셀',
    'cellpadding' => '셀 넓이',
    'cellspacing' => '셀 간격',
    'bg_color' => '배경 색',
    'background' => '배경 그림', // <=== new 1.0.6
    'error' => '에러',
    'error_rows_nan' => '행은 숫자로 입력하셔야 합니다.',
    'error_columns_nan' => '열은 숫자로 입력하셔야 합니다.',
    'error_width_nan' => '넓이는 숫자로 입력하셔야 합니다.',
    'error_height_nan' => '높이는 숫자로 입력하셔야 합니다.',
    'error_border_nan' => '선 두께는 숫자로 입력하셔야 합니다.',
    'error_cellpadding_nan' => '셀 넓이는 숫자로 입력하셔야 합니다.',
    'error_cellspacing_nan' => '셀 간격은 숫자로 입력하셔야 합니다.',
  ),
  'table_cell_prop' => array(
    'title' => '셀 속성',
    'horizontal_align' => '가로 정렬',
    'vertical_align' => '세로 정렬',
    'width' => '넓이',
    'height' => '높이',
    'css_class' => 'CSS 클래스',
    'no_wrap' => '줄 바꾸기 안함',
    'bg_color' => '배경 색',
    'background' => '배경 그림', // <=== new 1.0.6
    'ok' => '   확인   ',
    'cancel' => '취소',
    'left' => '왼쪽',
    'center' => '가운데',
    'right' => '오른쪽',
    'top' => '상단',
    'middle' => '중단',
    'bottom' => '하단',
    'baseline' => '기준선',
    'error' => '에러',
    'error_width_nan' => '넓이는 숫자로 입력하셔야 합니다.',
    'error_height_nan' => '높이는 숫자로 입력하셔야 합니다.',
  ),
  'table_row_insert' => array(
    'title' => '행 삽입'
  ),
  'table_column_insert' => array(
    'title' => '열 삽입'
  ),
  'table_row_delete' => array(
    'title' => '행 삭제'
  ),
  'table_column_delete' => array(
    'title' => '열 삭제'
  ),
  'table_cell_merge_right' => array(
    'title' => '열 합치기'
  ),
  'table_cell_merge_down' => array(
    'title' => '행 합치기'
  ),
  'table_cell_split_horizontal' => array(
    'title' => '행 나누기'
  ),
  'table_cell_split_vertical' => array(
    'title' => '열 나누기'
  ),
  'style' => array(
    'title' => '스타일'
  ),
  'font' => array(
    'title' => '글꼴'
  ),
  'fontsize' => array(
    'title' => '크기'
  ),
  'paragraph' => array(
    'title' => '문단'
  ),
  'bold' => array(
    'title' => '굵게'
  ),
  'italic' => array(
    'title' => '기울임'
  ),
  'underline' => array(
    'title' => '및줄'
  ),
  'ordered_list' => array(
    'title' => '번호 목록'
  ),
  'bulleted_list' => array(
    'title' => '기호 목록'
  ),
  'indent' => array(
    'title' => '들여쓰기'
  ),
  'unindent' => array(
    'title' => '내어쓰기'
  ),
  'left' => array(
    'title' => '왼쪽 정렬'
  ),
  'center' => array(
    'title' => '가운데 정렬'
  ),
  'right' => array(
    'title' => '오른쪽 정렬'
  ),
  'fore_color' => array(
    'title' => '전경색'
  ),
  'bg_color' => array(
    'title' => '배경색'
  ),
  'design_tab' => array(
    'title' => '위지윅모드'
  ),
  'html_tab' => array(
    'title' => 'HTML 모드'
  ),
  'colorpicker' => array(
    'title' => '색상 선택기',
    'ok' => '   확인   ',
    'cancel' => '취소',
  ),
  'cleanup' => array(
    'title' => 'HTML청소 (스타일 삭제)',
    'confirm' => '이 기능을 사용하면, 현재 문단에 사용된 글꼴이나, 태그들이 전체 또는 일부 삭제될 수 있습니다.',
    'ok' => '   확인   ',
    'cancel' => '취소',
  ),
  'toggle_borders' => array(
    'title' => 'Toggle borders',
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink',
    'url' => 'URL',
    'name' => 'Name',
    'target' => 'Target',
    'title_attr' => 'Title',
	'a_type' => 'Type', // <=== new 1.0.6
	'type_link' => '내부연결(Link)', // <=== new 1.0.6
	'type_anchor' => '외부연결(Anchor)', // <=== new 1.0.6
	'type_link2anchor' => 'Link to anchor', // <=== new 1.0.6
	'anchors' => 'Anchors', // <=== new 1.0.6
    'ok' => '   확인   ',
    'cancel' => '취소',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => '현재창 (_self)',
	'_blank' => '새창 (_blank)',
	'_top' => 'top frame (_top)',
	'_parent' => '부모창 (_parent)'
  ),
  'table_row_prop' => array(
    'title' => '행 속성',
    'horizontal_align' => '가로정렬',
    'vertical_align' => '세로정렬',
    'css_class' => 'CSS 클래스',
    'no_wrap' => '줄 바꾸기 안함',
    'bg_color' => '배경색',
    'ok' => '   확인   ',
    'cancel' => '취소',
    'left' => '왼쪽',
    'center' => '가운데',
    'right' => '오른쪽',
    'top' => '상단',
    'middle' => '중단',
    'bottom' => '하단',
    'baseline' => '기준선',
  ),
  'symbols' => array(
    'title' => '특수문자',
    'ok' => '   확인   ',
    'cancel' => '취소',
  ),
  'templates' => array(
    'title' => '템플릿',
  ),
  'page_prop' => array(
    'title' => '페이지 속성',
    'title_tag' => '타이틀',
    'charset' => '캐릭터 셋',
    'background' => '배경 그림',
    'bgcolor' => '배경색',
    'text' => '글자색',
    'link' => '링크 색',
    'vlink' => '방문한 링크 색',
    'alink' => '활성화된 링크 색',
    'leftmargin' => '왼쪽 여백',
    'topmargin' => '상단 여백',
    'css_class' => 'CSS 클래스',
    'ok' => '   확인   ',
    'cancel' => '취소',
  ),
  'preview' => array(
    'title' => '미리보기',
  ),
  'image_popup' => array(
    'title' => 'Image popup',
  ),
  'zoom' => array(
    'title' => '확대',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => '아래첨자',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => '위첨자',
  ),
);
?>