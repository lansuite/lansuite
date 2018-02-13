<?php

  ### Translation and Textoutput
    // This is how you start a new output
    // Use the t-function for each text you like to output. This function will translate the text to the users languge
    // To define textes in other languages, use the translation function within lansuite.
    // To provide the translations with your module, export the translations and attach them to your db.xml file
    $dsp->NewContent(t('Überschrift'), t('Erweiterte Überschrift'));

    // This is how to simply output some text
    $dsp->AddSingleRow(t('Eine einfache Zeile'));
    $dsp->AddDoubleRow("TEST : ", t('Eine einfache Zeile %1 und %2', "eins", "zwei"));


  ### Forms ###
    // Lets start a form
    // The first argument is the target-php, which will be loaded after submitting
    // In this case it will just reload this page without any effect
    $dsp->SetForm("index.php?mod=sample&action=show&step=2");

    // Text-Input
    // 1st argument: the html-name of the text-input, so you cann access the input after submitting by $_POST["name"]
    // 2nd argument: here you can write a text, which gives the user a hint what information he has to write in this field
    // 3rd argument: the default vaule
    // 4th argument: an errortext. i.e. if the submitted information is incomplete
    $dsp->AddTextFieldRow("name", t('Texteingabe'), "value", "");

    // Use this to display the form-submit-link
    // "add" is the text which is displayed on the button. It will be translatet into other languages, if translations exist
    $dsp->AddFormSubmitRow(t('Hinzufügen'));

    // This is how to load your own template, located in the 'templates'-folder of your module
    $dsp->AddSingleRow($smarty->fetch('modules/sample/templates/my_template.htm'));



  ### Direct DB access ###
    // Lets use the database - This will simply read all usernames from the database and display them
    $res = $db->qry("SELECT username FROM %prefix%user");
while ($user = $db->fetch_array($res)) {
    $user_out .= $user["username"] .", ";
}
    $dsp->AddSingleRow($user_out);

    $user_insg = $db->num_rows($res);
    $db->free_result($res);

    $dsp->AddSingleRow(t('Benutzer insgesamt') .": ". $user_insg);

    // This will finaly output all the $dsp-Rows
    $dsp->AddContent();
    
    
    
    ### Mastersearch ###
    // There is a quite simple way in lansuite to list and search data within data base tables, called mastersearch
  // In this exapmle we will list all news
  include_once('modules/mastersearch2/class_mastersearch2.php');
  $ms2 = new mastersearch2('news');

  // Define the source table and join all tables, which should be attached
  $ms2->query['from'] = "%prefix%news n LEFT JOIN %prefix%user u ON n.poster=u.userid";
  $ms2->query['default_order_by'] = 'DATE DESC';

  // How many entries will be displayed per page? defaults to 20, if not set
  $ms2->config['EntriesPerPage'] = 20;

  // If at least one AddTextSearchField is called, a form will be displayed to search within the results
  // exact = exact match; like = search musst be contained; fulltext = fulltext search; 1337 = like search with replacement of ! -> 1, 3 -> e, $ -> s, ...
  $ms2->AddTextSearchField(t('Titel'), array('n.caption' => 'like'));
  $ms2->AddTextSearchField(t('Text'), array('n.text' => 'fulltext'));
  $ms2->AddTextSearchField(t('Autor'), array('u.username' => '1337', 'u.name' => 'like', 'u.firstname' => 'like'));

  // Which columns should be displayed?
  $ms2->AddResultField(t('Titel'), 'n.caption');
  $ms2->AddSelect('u.userid');
  $ms2->AddResultField(t('Autor'), 'u.username', 'UserNameAndIcon');
  $ms2->AddResultField(t('Datum'), 'n.date', 'MS2GetDate');

  // These functions could be accessed for each row. To each link the group-by id is attached. See PrintSearch
  $ms2->AddIconField('details', 'index.php?mod=news&action=comment&newsid=', t('Details'));
if ($auth['type'] >= 2) {
    $ms2->AddIconField('edit', 'index.php?mod=news&action=change&step=2&newsid=', t('Editieren'));
}
if ($auth['type'] >= 3) {
    $ms2->AddIconField('delete', 'index.php?mod=news&action=delete&step=2&newsid=', t('Löschen'));
}

  // Use this to finaly print the search. first argument: the current url; second argument: the group-by-id this id will be unique in the result and will be attached to each AddIconField-link
  $ms2->PrintSearch('index.php?mod=sample&action=show', 'n.newsid');
  


  ### Masterform ###
  // If you like to insert data to the database, you could use the masterform class
  $dsp->NewContent(t('News verwalten'), t('Mit Hilfe des folgenden Formulars kannst du Neuigkeiten auf deiner Seite ergänzen und bearbeiten'));

  $mf = new masterform();

  // Define the db filds, which should be written. The second argument must be a valid db field, of the table supplied to SendForm
  $mf->AddField(t('Überschrift (Knappe Zusammenfassung für die Startseite)'), 'caption');
  $mf->AddField(t('Kategorie / Icon'), 'icon', IS_PICTURE_SELECT, 'ext_inc/news_icons', FIELD_OPTIONAL);
  $mf->AddField(t('Text'), 'text', '', LSCODE_ALLOWED);
  $selections = array();
  $selections['0'] = t('Normal');
  $selections['1'] = t('Wichtig');
  $mf->AddField(t('Priorität'), 'priority', IS_SELECTION, $selections, FIELD_OPTIONAL);

  // Maybe some values should not be added by the user, but set to fix values
  $mf->AddFix('date', time());
  $mf->AddFix('poster', $auth['userid']);

  // Sendform: 1) Current Link; 2) Affected data base table; 3) prim key in table; 4) ID to edit, if this is empty a new record will be added to the data base
if ($mf->SendForm('index.php?mod=sample&action=show', 'news', 'newsid', $_GET['newsid'])) {
    // Add additional actions here, that should be called, after successfully adding an entry
}
  

  
  ### Mastercomment ###
  // There are only two lines needed to add a comment function to the current table
    // Mastercomment: 1) which module should the comment belong to? 2) Which id should the comment referr to?
    new Mastercomment('news', $_GET['newsid']);
    
    
    
    ### Masterdelete ###
  // Use this, to delete entries from the data base
  $md = new masterdelete();
  // Will delete from the table 'news', where the fild 'newsid' is '$_GET['newsid']'
  // However a security question will be displayed first
  $md->Delete('news', 'newsid', $_GET['newsid']);
