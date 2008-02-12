<?php
// =========================================================
// SPAW PHP WYSIWYG editor control
// =========================================================
// German language file
// =========================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// German translation: Simon Schmitz, schmitz@unitedfuor.com
// Corrections: Matthias Höschele, matthias.hoeschele@gmx.net
// Copyright: Solmetra (c)2003 All rights reserved.
// ---------------------------------------------------------
//                                www.solmetra.com
// =========================================================
// v.1.0, 2003-04-10
// =========================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-1';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Ausschneiden'
  ),
  'copy' => array(
    'title' => 'Kopieren'
  ),
  'paste' => array(
    'title' => 'Einfügen'
  ),
  'undo' => array(
    'title' => 'Rückgängig'
  ),
  'redo' => array(
    'title' => 'Wiederherstellen'
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink einfügen'
  ),
  'internal_link' => array(                //Doesn't appear in Any lang-file?!? (dzy) ;-)
    'title' => 'Interner Link'
  ),
  'image_insert' => array(
    'title' => 'Bild einfügen',
    'select' => 'Auswählen',
	'delete' => 'Löschen', // new 1.0.5
    'cancel' => 'Abbrechen',
    'library' => 'Bibliothek',
    'preview' => 'Vorschau',
    'images' => 'Bild',
    'upload' => 'Bild Hochladen',
    'upload_button' => 'Hochladen',
    'error' => 'Fehler',
    'error_no_image' => 'Wählen Sie bitte ein Bild',
    'error_uploading' => 'Ein Fehler trat bei der Übertragung der Datei auf.  Bitte Versuchen Sie es später noch einmal.',
    'error_wrong_type' => 'Unzulässiger Dateityp',
    'error_no_dir' => 'Bibliothek ist physikalisch nicht vorhanden',
	'error_cant_delete' => 'Löschen fehlgeschlagen', // new 1.0.5

  ),
  'image_prop' => array(
    'title' => 'Bildeigenschaften',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
    'source' => 'Quelle',
    'alt' => 'Alternativer Text',
    'align' => 'Ausrichtung',
    'left' => 'Linksbündig',
    'right' => 'Rechtsbündig',
    'top' => 'Oben',
    'middle' => 'Mitte',
    'bottom' => 'Unten',
    'absmiddle' => 'Absolute Mitte',
    'texttop' => 'TextTop',
    'baseline' => 'An Grundlinie ausrichten',
    'width' => 'Breite',
    'height' => 'Höhe',
    'border' => 'Rand',
    'hspace' => 'Horizontaler Abstand',
    'vspace' => 'Vertikaler Abstand',
    'error' => 'Fehler',
    'error_width_nan' => 'Die Breite ist keine Zahl',
    'error_height_nan' => 'Die Höhe ist keine Zahl',
    'error_border_nan' => 'Der Rand ist keine Zahl',
    'error_hspace_nan' => 'Horizontaler Abstand ist keine Zahl',
    'error_vspace_nan' => 'Vertikaler Abstand ist keine Zahl',
  ),
  'hr' => array(
    'title' => 'Horizontale Linie'
  ),
  'table_create' => array(
    'title' => 'Tabelle einfügen'
  ),
  'table_prop' => array(
    'title' => 'Tabelleneigenschaften',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
    'rows' => 'Zeilen',
    'columns' => 'Spalten',
    'css_class' => 'CSS Klasse', // <=== new 1.0.6
    'width' => 'Breite',
    'height' => 'Höhe',
    'border' => 'Rand',
    'pixels' => 'Pixel',
    'cellpadding' => 'Zellauffüllung',
    'cellspacing' => 'Zellabstand',
    'bg_color' => 'Hintergrundfarbe',
    'background' => 'Hintergrundbild', // <=== new 1.0.6
    'error' => 'Fehler',
    'error_rows_nan' => 'Die Zeilenanzahl ist keine Zahl',
    'error_columns_nan' => 'Die Spaltenanzahl ist keine Zahl',
    'error_width_nan' => 'Die Breite ist keine Zahl',
    'error_height_nan' => 'Die Höhe ist keine Zahl',
    'error_border_nan' => 'Die Randbreite ist keine Zahl',
    'error_cellpadding_nan' => 'Zellauffüllung ist keine Zahl',
    'error_cellspacing_nan' => 'Zellabstand ist keine Zahl',
  ),
  'table_cell_prop' => array(
    'title' => 'Zelleigenschaften',
    'horizontal_align' => 'Horizontale Ausrichtung',
    'vertical_align' => 'Vertikale Ausrichtung',
    'width' => 'Breite',
    'height' => 'Höhe',
    'css_class' => 'CSS Klasse',
    'no_wrap' => 'Zeilenumbruch verhindern',
    'bg_color' => 'Hintergrundfarbe',
    'background' => 'Hintergrundbild', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
    'left' => 'Links',
    'center' => 'Zentriert',
    'right' => 'Rechts',
    'top' => 'Oben',
    'middle' => 'Mitte',
    'bottom' => 'Unten',
    'baseline' => 'Grundlinie',
    'error' => 'Fehler',
    'error_width_nan' => 'Die Breite ist keine Zahl',
    'error_height_nan' => 'Die Höhe ist keine Zahl',
    
  ),
  'table_row_insert' => array(
    'title' => 'Zeile einfügen'
  ),
  'table_column_insert' => array(
    'title' => 'Spalte einfügen'
  ),
  'table_row_delete' => array(
    'title' => 'Zeile löschen'
  ),
  'table_column_delete' => array(
    'title' => 'Spalte löschen'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Zelle mit rechts daneben liegender verbinden'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Zelle mit darunter liegender verbinden'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Zelle horizontal teilen'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Zelle vertikal teilen'
  ),
  'style' => array(
    'title' => 'Style'
  ),
  'font' => array(
    'title' => 'Schriftart'
  ),
  'fontsize' => array(
    'title' => 'Schriftgrad'
  ),
  'paragraph' => array(
    'title' => 'Formatvorlage'
  ),
  'bold' => array(
    'title' => 'Fett'
  ),
  'italic' => array(
    'title' => 'Kursiv'
  ),
  'underline' => array(
    'title' => 'Unterstrichen'
  ),
  'ordered_list' => array(
    'title' => 'Nummerierung'
  ),
  'bulleted_list' => array(
    'title' => 'Aufzählungszeichen'
  ),
  'indent' => array(
    'title' => 'Einzug vergrößern'
  ),
  'unindent' => array(
    'title' => 'Einzug verkleinern'
  ),
  'left' => array(
    'title' => 'Linksbündig'
  ),
  'center' => array(
    'title' => 'Zentriert'
  ),
  'right' => array(
    'title' => 'Rechtsbündig'
  ),
  'justify' => array(     // <- 1.0.5
    'title' => 'Blocksatz'
  ),
  'fore_color' => array(
    'title' => 'Schriftfarbe'
  ),
  'bg_color' => array(
    'title' => 'Hintergrundfarbe'
  ),
  'design_tab' => array(
    'title' => 'Zum WYSIWYG (Design) Modus wechseln'
  ),
  'html_tab' => array(
    'title' => 'Zum HTML (Quelltext) Modus wechseln'
  ),
  'colorpicker' => array(
    'title' => 'Farbpipette',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
  ),
  // <<<<<<<<< NEW >>>>>>>>>
  'cleanup' => array(
    'title' => 'HTML Säuberung (Stile entfernen)',
    'confirm' => 'Das Ausführen dieser Aktion wird alle Stile, Schriften und nutzlose Tags vom aktuellen Inhalt entfernen. Die Formatierung kann teilweise oder vollständig verloren gehen.',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
  ),
  'toggle_borders' => array(
    'title' => 'Ränder anzeigen',
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink',
    'url' => 'URL',
    'name' => 'Name',
    'target' => 'Ziel',
    'title_attr' => 'Titel',
	'a_type' => 'Typ', // <=== new 1.0.6
	'type_link' => 'Link', // <=== new 1.0.6
	'type_anchor' => 'Anker', // <=== new 1.0.6
	'type_link2anchor' => 'Link -> Anker', // <=== new 1.0.6
	'anchors' => 'Anker', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
  ),
  'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'Eigenes Fenster (_self)',
	'_blank' => 'Neues Fenster (_blank)',
	'_top' => 'Hauptfenster (_top)',
	'_parent' => 'Aufrufendes Fenster (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Zeileneigenschaften',
    'horizontal_align' => 'Horizontale Ausrichtung',
    'vertical_align' => 'Vertikale Ausrichtung',
    'css_class' => 'CSS Klasse',
    'no_wrap' => 'Zeilenumbruch verhindern',
    'bg_color' => 'Hintergrundfarbe',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
    'left' => 'Links',
    'center' => 'Zentriert',
    'right' => 'Rechts',
    'top' => 'Oben',
    'middle' => 'Mitte',
    'bottom' => 'Unten',
    'baseline' => 'Grundlinie',
  ),
  'symbols' => array(
    'title' => 'Sonderzeichen',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
  ),
  'symbols' => array(
    'title' => 'Sonderzeichen',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
  ),
  'templates' => array(
    'title' => 'Templates',
  ),
  'page_prop' => array(
    'title' => 'Seiteneigenschaften',
    'title_tag' => 'Titel',
    'charset' => 'Dokumentkodierung',
    'background' => 'Hintergrundbild',
    'bgcolor' => 'Hintergrundfarbe',
    'text' => 'Textfarbe',
    'link' => 'Linkfarbe',
    'vlink' => 'Besuchter-Link-Farbe',
    'alink' => 'Aktiver-Link-Farbe',
    'leftmargin' => 'Linker Rand',
    'topmargin' => 'Oberer Rand',
    'css_class' => 'CSS Klasse',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
  ),
  'preview' => array(
    'title' => 'Vorschau',
  ),
  'image_popup' => array(
    'title' => 'Bild-Popup',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => 'Tiefgestellt',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => 'Hochgestellt',
  ),
);
?>


