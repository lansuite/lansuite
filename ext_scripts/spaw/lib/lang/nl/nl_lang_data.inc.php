<?php 
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Dutch language file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// Translation to dutch: Koen Koppens, k.koppens@home.nl
// Translation updated: Alex Timmermans, alex@artbizz.com
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.1, 2004-12-30
// ================================================

// charset to be used in dialogs
$spaw_lang_charset = 'iso-8859-1';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$spaw_lang_data = array(
  'cut' => array(
    'title' => 'Knippen'
  ),
  'copy' => array(
    'title' => 'Kopiëren'
  ),
  'paste' => array(
    'title' => 'Plakken'
  ),
  'undo' => array(
    'title' => 'Ongedaan maken'
  ),
  'redo' => array(
    'title' => 'Opnieuw'
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink'
  ),
  'image_insert' => array(
    'title' => 'Afbeelding invoeren',
    'select' => 'Selecteren',
	'delete' => 'Verwijderen', // new 1.0.5
    'cancel' => 'Annuleren',
    'library' => 'Bibliotheek',
    'preview' => 'Voorbeeld',
    'images' => 'Afbeeldingen',
    'upload' => 'Afbeelding uploaden',
    'upload_button' => 'Upload',
    'error' => 'Fout',
    'error_no_image' => 'Selecteer een afbeelding',
    'error_uploading' => 'Er is een fout opgetreden bij het uploaden van het bestand. Probeert u het later nogmaals',
    'error_wrong_type' => 'Verkeerd afbeelding bestandstype',
    'error_no_dir' => 'De bibliotheek is niet beschikbaar',
	'error_cant_delete' => 'Het verwijderen is mislukt!', // new 1.0.5
  ),
  'image_prop' => array(
    'title' => 'Afbeelding eigenschappen',
    'ok' => '   OK   ',
    'cancel' => 'Annuleren',
    'source' => 'Bron',
    'alt' => 'Alternatieve tekst',
    'align' => 'Uitlijnen',
    'left' => 'linkerkant',
    'right' => 'rechterkant',
    'top' => 'bovenkant',
    'middle' => 'midden',
    'bottom' => 'bottom',
    'absmiddle' => 'absolute midden',
    'texttop' => 'bovenkant tekst',
    'baseline' => 'Onderste lijn',
    'width' => 'Breedte',
    'height' => 'Hoogte',
    'border' => 'Rand',
    'hspace' => 'Hor. ruimte',
    'vspace' => 'Vert. ruimte',
    'error' => 'Fout',
    'error_width_nan' => 'Breedte is geen getal',
    'error_height_nan' => 'Hoogte is geen getal',
    'error_border_nan' => 'Rand is geen getal',
    'error_hspace_nan' => 'Horizontale ruimte is geen getal',
    'error_vspace_nan' => 'Vertikale ruimte is geen getal',
  ),
  'hr' => array(
    'title' => 'Horizontale liniëring'
  ),
  'table_create' => array(
    'title' => 'Tabel crëeren'
  ),
  'table_prop' => array(
    'title' => 'Tabel eigenschappen',
    'ok' => '   OK   ',
    'cancel' => 'Annuleren',
    'rows' => 'Rijen',
    'columns' => 'Kolommen',
    'width' => 'Breedte',
    'height' => 'Hoogte',
    'border' => 'Rand',
    'pixels' => 'pixels',
    'cellpadding' => 'Cel opvulling',
    'cellspacing' => 'Cel opvulling',
    'bg_color' => 'Achtergrond kleur',
    'error' => 'Fout',
	'background' => 'Achtergrond afbeelding', // <=== new 1.0.6
    'error' => 'Fout',
    'error_rows_nan' => 'Opgegeven rij(en) zijn geen getallen',
    'error_columns_nan' => 'Opgegeven kolom(men) zijn geen getallen',
    'error_width_nan' => 'Breedte is geen getal',
    'error_height_nan' => 'Hoogte is geen getal',
    'error_border_nan' => 'Rand is geen getal',
    'error_cellpadding_nan' => 'Cel opvulling is geen getal',
    'error_cellspacing_nan' => 'Cel opvulling is geen getal',
  ),
  'table_cell_prop' => array(
    'title' => 'Cel eigenschappen',
    'horizontal_align' => 'Horizontale uitlijning',
    'vertical_align' => 'Vertikale uitlijning',
    'width' => 'Breedte',
    'height' => 'Hoogte',
    'css_class' => 'CSS klasse',
    'no_wrap' => 'Regels niet afbreken',
    'bg_color' => 'Achtergrond kleur',
	'background' => 'Achtergrond afbeelding', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Annulerenl',
    'left' => 'Linkerkant',
    'center' => 'Centreren',
    'right' => 'Rechterkant',
    'top' => 'Bovenkant',
    'middle' => 'Midden',
    'bottom' => 'Onderkant',
    'baseline' => 'Onderste lijn',
    'error' => 'Fout',
    'error_width_nan' => 'Breedte is geen getal',
    'error_height_nan' => 'Hoogte is geen getal',
    
  ),
  'table_row_insert' => array(
    'title' => 'Rij invoeren'
  ),
  'table_column_insert' => array(
    'title' => 'Kolom invoeren'
  ),
  'table_row_delete' => array(
    'title' => 'Rij verwijderen'
  ),
  'table_column_delete' => array(
    'title' => 'Kolom verwijderen'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Naar rechts samenvoegen'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Naar beneden samenvoegen'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Cellen horizontaal splitsen'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Cellen vertikaal splitsen'
  ),
  'style' => array(
    'title' => 'Opmaak'
  ),
  'font' => array(
    'title' => 'Lettertype'
  ),
  'fontsize' => array(
    'title' => 'Grootte'
  ),
  'paragraph' => array(
    'title' => 'Alinea'
  ),
  'bold' => array(
    'title' => 'Vet'
  ),
  'italic' => array(
    'title' => 'Schuingedrukt'
  ),
  'underline' => array(
    'title' => 'Onderstrepen'
  ),
  'ordered_list' => array(
    'title' => 'Genummerde opsomming'
  ),
  'bulleted_list' => array(
    'title' => 'Ongesorteerde opsomming'
  ),
  'indent' => array(
    'title' => 'Inspringen'
  ),
  'unindent' => array(
    'title' => 'Terugspringen'
  ),
  'left' => array(
    'title' => 'Links'
  ),
  'center' => array(
    'title' => 'Midden'
  ),
  'right' => array(
    'title' => 'Rechts'
  ),
  'fore_color' => array(
    'title' => 'Voorgrond kleur'
  ),
  'bg_color' => array(
    'title' => 'Achtergrond kleur'
  ),
  'design_tab' => array(
    'title' => 'Schakel over naar WYSIWYG (ontwerp) modus'
  ),
  'html_tab' => array(
    'title' => 'Schakel over naar HTML (kode) modus'
  ),
  'colorpicker' => array(
    'title' => 'Kleur selecteren',
    'ok' => '   OK   ',
    'cancel' => 'Annuleren',
  ),
  'cleanup' => array(
    'title' => 'HTML opruimen (verwijder opmaak)',
    'confirm' => 'Als u deze actie uitvoert, zal alle opmaak, lettertypen en onnodige tags worden verwijdert van de huidige opmaak. Een deel of de gehele opmaak van uw tekst zal verloren gaan.',
    'ok' => '   OK   ',
    'cancel' => 'Annuleren',
  ),
  'toggle_borders' => array(
    'title' => 'Overschakeleren rand',
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink',
    'url' => 'URL',
    'name' => 'Naam',
    'target' => 'Doel',
    'title_attr' => 'Titel',
	'a_type' => 'Type', // <=== new 1.0.6
	'type_link' => 'Link', // <=== new 1.0.6
	'type_anchor' => 'Anker', // <=== new 1.0.6
	'type_link2anchor' => 'Link naar anker', // <=== new 1.0.6
	'anchors' => 'Anker', // <=== new 1.0.6
    'ok' => '   OK   ',
    'cancel' => 'Annuleren',
  ),
    'hyperlink_targets' => array( // <=== new 1.0.5
  	'_self' => 'dit frame (_self)',
	'_blank' => 'nieuw scherm (_blank)',
	'_top' => 'top frame (_top)',
	'_parent' => 'ouderlijk frame (_parent)'
  ),
  'table_row_prop' => array(
    'title' => 'Rij eigenschappen',
    'horizontal_align' => 'Horizontaal uitlijnen',
    'vertical_align' => 'Verticaal uitlijnen',
    'css_class' => 'CSS klasse',
    'no_wrap' => 'Tekst niet uitvullen',
    'bg_color' => 'Achtergrond kleur',
    'ok' => '   OK   ',
    'cancel' => 'Annuleren',
    'left' => 'Links',
    'center' => 'Midden',
    'right' => 'Rechts',
    'top' => 'Bovenkant',
    'middle' => 'Midden',
    'bottom' => 'Onderkant',
    'baseline' => 'Onderste lijn',
  ),
  'symbols' => array(
    'title' => 'Speciale karakters',
    'ok' => '   OK   ',
    'cancel' => 'Annuleren',
  ),
  'templates' => array(
    'title' => 'Sjablonen',
  ),
  'page_prop' => array(
    'title' => 'Pagina instellingen',
    'title_tag' => 'Titel',
    'charset' => 'Karakterset',
    'background' => 'Achtergrond afbeelding',
    'bgcolor' => 'Achtergrond kleur',
    'text' => 'Tekst kleur',
    'link' => 'Link leuk',
    'vlink' => 'Bezochte link kleur',
    'alink' => 'Actieve link kleur',
    'leftmargin' => 'Marge linkerkant',
    'topmargin' => 'Marge bovenkant',
    'css_class' => 'CSS klasse',
    'ok' => '   OK   ',
    'cancel' => 'Annuleren',
  ),
  'preview' => array(
    'title' => 'Voorbeeld',
  ),
  'image_popup' => array(
    'title' => 'Afbeelding popup',
  ),
  'zoom' => array(
    'title' => 'Zoom in',
  ),
  'subscript' => array( // <=== new 1.0.7
    'title' => 'Subschrift',
  ),
  'superscript' => array( // <=== new 1.0.7
    'title' => 'Superschrift',
  ),
);
?>

