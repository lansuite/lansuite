//// Basic Events ////
function BodyOnload() {
  // Load Google-Maps, if map-element is found
  if (document.getElementById("GoogleMaps")) ShowMap();

  // Load nifty-corners
  Nifty("ul.BoxHeadline", "top");
  Nifty("ul.BoxContent", "bottom");
  Nifty("div#Content", "big");
  Nifty("li.Confirmation", "big");
  Nifty("li.Information", "big");
  Nifty("li.Err", "big");
  Nifty("div.Button a", "transparent same-height");
}


//// Basic ////

function InsertCode(object, CodeStart, CodeEnd) {
  if (!CodeEnd) CodeEnd = '';

  if (CodeStart != '') {
    object.focus();

    if (document.selection) {
      sel = document.selection.createRange();
      selected = sel.text;
      sel.text = CodeStart + selected + CodeEnd;

    } else if (object.selectionStart || object.selectionStart == '0') {
      var startPos = object.selectionStart;
      var endPos = object.selectionEnd;
      object.value = object.value.substring(0, startPos) + CodeStart + object.value.substring(startPos, endPos) + CodeEnd + object.value.substring(endPos, object.value.length);

    } else object.value += CodeStart + CodeEnd;
  }
}

function OpenWindow(url, name) {
  url = url.replace(/\&amp\;/g, "&");
  Win1 = window.open(url, name, "width=600,height=200,left=100,top=100");
  Win1.focus();
}

function OpenPreviewWindow(url, obj) {
  text = obj.value.replace(/\n/g, "--NEWLINE--");
  url += '&text=' + text;
  OpenWindow(url, 'Vorschau');
}

function OpenHelplet(module, helpletid) {
  w = window.open('index.php?mod=helplet&action=helplet&design=base&module='+ module +'&helpletid='+ helpletid, 'neu', 'width=800, height=500, resizable=yes, scrollbars=yes');
}


//// AJAX ////

// globale Instanz von XMLHttpRequest
var xmlHttp = false;

// XMLHttpRequest-Instanz erstellen
// ... für Internet Explorer
try {
  xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
} catch(e) {
  try {
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  } catch(e) {
    xmlHttp = false;
  }
}
// ... für Mozilla, Opera und Safari
if (!xmlHttp && typeof XMLHttpRequest != 'undefined') xmlHttp = new XMLHttpRequest();

// aktuelle Daten laden
function loadPage(url) {
  location.href = url;

// AJAX deactivated
/*
  LoadingToolTip('<b>Loading...</b>');
  if (xmlHttp) {
    xmlHttp.open('GET', url +'&contentonly=1');
    xmlHttp.onreadystatechange = function () {
      if (xmlHttp.readyState == 4) {
        // If target has script inside, use normal redirect, for the script wont run otherwise
        if (xmlHttp.responseText.indexOf("<script ") > 0) location.href = url;
        else {
          document.getElementById("LScontent").innerHTML = xmlHttp.responseText;
          document.getElementById("LSfooter").innerHTML = document.getElementById("NewLSfooter").innerHTML;
          document.getElementById("LScontent").removeChild(document.getElementById("NewLSfooter"));
        }
        document.getElementById("LSloading").style.visibility = "hidden";
      }
    }
    xmlHttp.send(null);
  }
*/
}

function LoadingToolTip(text) {
  if (window.event) {
    var scr = TX_getScrollPos();
    var cordX = window.event.clientX + scr.x;
    var cordY = window.event.clientY + scr.y;
  }
  
  document.getElementById("LSloading").style.position = "absolute";
  document.getElementById("LSloading").style.left =  ( cordX + 20 ) + "px";
  document.getElementById("LSloading").style.top = ( cordY + 20 ) + "px";
  document.getElementById("LSloading").innerHTML = text;
  document.getElementById("LSloading").style.visibility = "visible";
}


//// Class Display ////

function changepic(picname, obj) {
  if (picname == "none" || picname == "") obj.src = "design/standard/images/index_transparency.gif";
  else obj.src = picname;
}

function TextAreaPlusCharsLeft(textarea, counter, maxchar){
	if (textarea.value.length > maxchar) {
		textarea.value = textarea.value.substr(0, maxchar);
		textarea.blur();
	}
	counter.value = maxchar - textarea.value.length;
}

function CheckBoxBoxActivate(name, id) {
  if (id) document.getElementById(name).style.display = "";
  else document.getElementById(name).style.display = "none";
}

function DropDownBoxActivate(name, id, list) {
  if (list) {
    var found = false;
    for (var x = 0; x < list.length; x++) if (list[x] == id) found = true;
    if (!found) document.getElementById(name).style.display = "none";
    else document.getElementById(name).style.display = "";
  } else {
    if (id <= 0) document.getElementById(name).style.display = "none";
    else document.getElementById(name).style.display = "";

    var Preisliste = document.getElementsByName("price_id")[0];
    if (Preisliste) {
      // Delete all current prices    
      for(z = 0; z < Preisliste.length; z++) Preisliste.remove(z);
  
      // Add new prices
      LoadingToolTip('<b>Loading new pricelist...</b>');
      if (xmlHttp) {
        xmlHttp.open('GET', 'index.php?mod=usrmgr&action=pricelist&design=base&party_id='+ document.getElementsByName("party_id")[0].value);
        xmlHttp.onreadystatechange = function () {
          if (xmlHttp.readyState == 4) {
            var Lines = xmlHttp.responseText.split("\r");
            for (CurLine = 0; CurLine < Lines.length; CurLine++) {
              var Row = Lines[CurLine].split("%");
              if (Row[0] != "") {
                var Eintrag = document.createElement("option");
                Eintrag.text = Row[0];
                Eintrag.value = Row[1];
                var FolgendeOption = null;
                if (document.all) FolgendeOption = Auswahlliste.length;
                Preisliste.add(Eintrag, FolgendeOption);
              }
            }
            document.getElementById("LSloading").style.visibility = "hidden";
          }
        }
        xmlHttp.send(null);
      }
    }
  }
}

function CheckPasswordSecurity(password, ImgObj) {
	var TestNumberOfChars = false;
	var TestUppercaseChars = false;
	var TestLowercaseChars = false;
	var TestDigits = false;
	var TestSpecialChars = false;
	var TestCounter = false;
	var TestCounter2 = false;
	var TestWhiteSpaces = false;

	var counter = 0;

	/* Check if length of password is greather then 8 */
	if (password.length >= 8) TestNumberOfChars = true;

	/* Check for minimum of 2 uppercase Letters */
	if (password.match(/[A-Z].*[A-Z]/)) TestUppercaseChars = true;

	/* Check for minimum of 2 lowercase letters */
	if (password.match(/[a-z].*[a-z]/)) TestLowercaseChars = true;

	/* Check for minimum of 2 digits */
	if (password.match(/[0-9].*[0-9]/)) TestDigits = true;

	/* Check for minimum of 2 special chars */
	var specCharCounter = 0;
	if (password.length > 0) {
		characters = password.split("");

		for (var i = 0; i < characters.length; i++) {
			singleChar = characters[i];
			if (singleChar.match(/[^a-zA-Z0-9]/)) specCharCounter++;
		}

		if (specCharCounter >= 2) TestSpecialChars = true;
	}

	/* Check for whitespaces and ESC-Sequences */
	if (password.match(/\s/)) TestWhiteSpaces = true;

	/* Check for minimum of 2 true ifs */
	if (TestUppercaseChars) counter++;
	if (TestLowercaseChars) counter++;
	if (TestDigits) counter++;
	if (TestSpecialChars) counter++;

	if ((counter >= 2) && (!TestWhiteSpaces)) TestCounter = true;

	if ((counter >= 3) && !(TestWhiteSpaces)) TestCounter2 = true;

	var zaehler = 0;
	if (TestCounter) zaehler++;
	if (TestCounter2) zaehler++;
	if (TestNumberOfChars) zaehler++;

	ImgObj.src = 'design/images/password_bar'+ zaehler +'.jpg';
}


//// Mastersearch2 ////

// Highlights the current table row under the mouse
function markieren(EintragSpalte) {
  if (typeof(document.getElementsByTagName) != 'undefined') var Spalten = EintragSpalte.getElementsByTagName('td');
  else if (typeof(EintragSpalte.cells) != 'undefined') var Spalten = EintragSpalte.cells;
  else return false;

  for (var c = 0; c < Spalten.length; c++) Spalten[c].className = 'row_value_important';

  return true;
}

// Un-Highlights the current table row under the mouse
function unmarkieren(EintragSpalte) {
  if (typeof(document.getElementsByTagName) != 'undefined') var Spalten = EintragSpalte.getElementsByTagName('td');
  else if (typeof(EintragSpalte.cells) != 'undefined') var Spalten = EintragSpalte.cells;
  else return false;

  for (var c = 0; c < Spalten.length; c++) Spalten[c].className = 'row_value';
  
  return true;
}

// Checks all/none/inverted checkboxes
function change_selection() {
  if (document.ms_result.action_select.value == "") return 0;
	else if (document.ms_result.action_select.value == "select_all") for (z=0; z<=document.ms_result.length-1; z++){
		document.ms_result.elements[z].checked = 1;
	} else if (document.ms_result.action_select.value == "select_none") for (z=0; z<=document.ms_result.length-1; z++){
		document.ms_result.elements[z].checked = 0;
	} else if (document.ms_result.action_select.value == "select_invert") for (z=0; z<=document.ms_result.length-1; z++){
		if (document.ms_result.elements[z].checked) document.ms_result.elements[z].checked = 0;
		else document.ms_result.elements[z].checked = 1;
	} else {
	  if (MultiSelectSecurityQuest[document.ms_result.action_select.value] == 1) {
      if (!confirm("Wollen Sie die Aktion '"+ document.ms_result.action_select.options[document.ms_result.action_select.selectedIndex].text +"' wirklich auf alle ausgewählten Einträge anwenden?")) return 0;
    }
    MultiSelectActions[document.ms_result.action_select.value] = MultiSelectActions[document.ms_result.action_select.value].replace(/&amp;/g, "&");
    document.ms_result.action = MultiSelectActions[document.ms_result.action_select.value];
    document.ms_result.submit();
  }
}


//// Search Box ////
function SubmitDropDown(FormObj, DropDownObj) {
  FormObj.action = DropDownObj.value;
  FormObj.submit();
}


//// Seating ////

// Define marker for images
var setdata;

// Define Mouseflag
var mouseflag = 0;

// Functions for preload images
function MM_preloadImages() {
	var d=document;
	if (d.images) {
		if (!d.MM_p) d.MM_p = new Array();
		var i, j = d.MM_p.length, a = MM_preloadImages.arguments;
		for (i = 0; i < a.length; i++) if (a[i].indexOf("#")!=0) {
			d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];
		}
	}
}

// Functions for find object
function MM_findObj(n, d) {
	var p, i, x;
	if (!d) d = document; 
	if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
		d = parent.frames[n.substring(p + 1)].document;
		n = n.substring(0, p);
	}
	if (!(x = d[n]) && d.all) x = d.all[n];
	for (i = 0; !x && i < d.forms.length; i++) x = d.forms[i][n];
	for (i = 0; !x && d.layers && i < d.layers.length; i++) x = MM_findObj(n, d.layers[i].document);
	if (!x && document.getElementById) x = document.getElementById(n);
	return x;
}

// Functions for swap image 
function MM_swapImage() {
	MM_swapImgRestore();
	var i, j = 0, x, a = MM_swapImage.arguments;
	document.MM_sr = new Array;
	for (i = 0; i < (a.length - 2); i += 3)
	if ((x = MM_findObj(a[i])) != null) {
		document.MM_sr[j++] = x;
		if(!x.oSrc) x.oSrc = x.src;
		x.src = a[i + 2];
	}
}

// Functions for restore image 
function MM_swapImgRestore() {
  	var i, x, a = document.MM_sr;
	for (i = 0; a && i < a.length && (x = a[i]) && x.oSrc; i++) x.src = x.oSrc;
}

// Functions to Change Image
function changeImage(id){
	var i = 0;
	if (mouseflag == 1){
    var StartPos = 0; 
		//img = CurrentPicture.substring(0, CurrentPicture.length - 4);
		if (CurrentPicture.indexOf("lsthumb_") > 0) StartPos = CurrentPicture.indexOf("lsthumb_") + 8;
		else StartPos = CurrentPicture.lastIndexOf("/") + 1;
		img = CurrentPicture.substring(StartPos, CurrentPicture.length - 4);
		if (id != 'null' && document.getElementById(id.id.substring(1, id.id.length)).value != img){
			//id.style.backgroundImage = "url("+ image[img].src +")";
			id.style.backgroundImage = "url("+ CurrentPicture +")";
			document.getElementById(id.id.substring(1,id.id.length)).value = img;
		}
	}
}

var CurrentPicture = '';
function UpdateCurrentPicture(file) {
  CurrentPicture = file;
  document.images["PreviewIcon"].src = file;
}


function AllselectH(rowid) {
  mouseflag = 1;
	for (var x = 0; x < cols; x++) {
		var zahl = x * 100 + rowid;
    changeImage(document.getElementById("fcell"+zahl));
	}
  mouseflag = 0;
}


function AllselectV(colid) {
  mouseflag = 1;
	for (var x = 0; x < rows; x++) {
		var zahl = colid * 100 + x;
    changeImage(document.getElementById("fcell"+zahl));
	}
  mouseflag = 0;
}


// Functions for details an swap
function showseat(id) {
	if (id != 'null' && setdata != id) {
		// Split seatdata 
		data_array = seat[id].split(",");

		// Swap image
		MM_swapImgRestore();

		// Write userdata
		document.getElementById("seating").firstChild.nodeValue = 'Block: ' + data_array[4];
		document.getElementById("name").firstChild.nodeValue = data_array[0];
		if (data_array[1] || data_array[2]) document.getElementById("name").firstChild.nodeValue += ' (' + data_array[1] + ' ' + data_array[2] + ')';
		document.getElementById("clan").firstChild.nodeValue = data_array[3];
		document.getElementById("ip").firstChild.nodeValue = data_array[7];

		switch (data_array[6]) {
			case "1":
					document.getElementById("seating").firstChild.nodeValue += ' [Frei]';
					MM_swapImage(id,'','ext_inc/auto_images/{default_design}/seat/seat_free_onclick.png',1);
			break;
			case "2": 
					document.getElementById("seating").firstChild.nodeValue += ' [Belegt]';
					MM_swapImage(id,'','ext_inc/auto_images/{default_design}/seat/seat_reserved_onclick.png',1);
			break;
			case "3": 
					document.getElementById("seating").firstChild.nodeValue += ' [Vorgemerkt]';
					MM_swapImage(id,'','ext_inc/auto_images/{default_design}/seat/seat_marked_onclick.png',1);
			break;
			case "8":
					document.getElementById("seating").firstChild.nodeValue += ' [Belegt8]';
					MM_swapImage(id,'','ext_inc/auto_images/{default_design}/seat/seat_myselfe_onclick.png',1);
			break;
			case "9":
					document.getElementById("seating").firstChild.nodeValue += ' [Belegt9]';
					MM_swapImage(id,'','ext_inc/auto_images/{default_design}/seat/seat_clanmate_onclick.png',1);
			break;
			default:
					document.getElementById("seating").firstChild.nodeValue += ' [Umgebung]';
			break;
		}

		setdata = id;
	}
}

function InitSeating() {
  MM_preloadImages('ext_inc/auto_images/{default_design}/seat/seat_free_onclick.png'); 
  MM_preloadImages('ext_inc/auto_images/{default_design}/seat/seat_reserved_onclick.png'); 
  MM_preloadImages('ext_inc/auto_images/{default_design}/seat/seat_marked_onclick.png'); 
  MM_preloadImages('ext_inc/auto_images/{default_design}/seat/seat_myselfe_onclick.png'); 
  MM_preloadImages('ext_inc/auto_images/{default_design}/seat/seat_clanmate_onclick.png');
  
  var flagV = new Array();
  var flagH = new Array();
  
  for (i = 0; i <= cols; i++) {
  	flagV[i] = false;
  }
  
  for (i = 0; i <= rows; i++) {
  	flagH[i] = false;
  } 
}

function setFlag(Ereignis){
	mouseflag = 1;
}

function resetFlag(Ereignis){
	mouseflag = 0;
}

// Tooltip windows
function TX_getScrollPos() { 
   if (document.body.scrollTop != undefined && navigator.appName.indexOf("Explorer") != -1 ) { 
      var res = (document.compatMode != "CSS1Compat") ? document.body : document.documentElement; 
      return {x : res.scrollLeft, y : res.scrollTop}; 
   } 
   else { 
      return {x : window.pageXOffset, y : window.pageYOffset}; 
   } 
 
} 
 
function TX_showToolTip(e,id) { 
 	if (id != 'null') {
 		
        var scr = TX_getScrollPos(); 
        var cordX = e.clientX + scr.x; 
        var cordY = e.clientY + scr.y; 
 		var text = "<table>";
 		
 		data_array = seat[id].split(",");
 		
 		// Text für die Verschiedenen Symbole
 		switch (data_array[6]){
		
		case "2":
		case "3":
		case "8":
		case "9":
 			text += '<tr><td style="font-weight: bold;">Block:</td><td>' + data_array[4] + "</td></tr>";
        	text += '<tr><td style="font-weight: bold;">Benutzername:</td><td>' + data_array[0] + "</td></tr>";
        	text += '<tr><td style="font-weight: bold;">Name / Vorname:</td><td>' + data_array[1] + ' ' + data_array[2] + "</td></tr>";
        	text += '<tr><td style="font-weight: bold;">Clan :</td><td>' + data_array[3] + "</td></tr>";
        	text += '<tr><td style="font-weight: bold;">IP :</td><td>' + data_array[7] + "</td></tr>";
        	break;
        case "1":
        	text += '<tr><td style="font-weight: bold;">Block :</td><td>' + data_array[4] + " Frei</td></tr>";
        	text += '<tr><td style="font-weight: bold;">IP :</td><td>' + data_array[7] + "</td></tr>";
        	break;
        
        case "7":
        	text += '<tr><td style="font-weight: bold;">Block :</td><td>' + data_array[4] + " Gesperrt</td></tr>";
        	text += '<tr><td style="font-weight: bold;">IP :</td><td>' + data_array[7] + "</td></tr>";
        	break;
  
        // Text für die Verschiedenen Symbole
        case "80":
        case "81":
        	text += '<tr><td style="font-weight: bold;">Block :</td><td>' + data_array[4] + "</td></tr>";
        	text += '<tr><td style="font-weight: bold;">Beschreibung :</td><td> WC</td></tr>';
        break;

        case "82":
        	text += '<tr><td style="font-weight: bold;">Block :</td><td>' + data_array[4] + "</td></tr>";
        	text += '<tr><td style="font-weight: bold;">Beschreibung :</td><td> Notausgang</td></tr>';
        break;
        
        case "83":
        	text += '<tr><td style="font-weight: bold;">Block :</td><td>' + data_array[4] + "</td></tr>";
        	text += '<tr><td style="font-weight: bold;">Beschreibung :</td><td> Catering</td></tr>';
        break;
        
        default:
			
			text = "";
			break;
 		}
 		if(text != ""){
 			text += "</table>"
	        document.getElementById("tooltip").style.position = "absolute"; 
    	    document.getElementById("tooltip").style.left =  ( cordX + 20 ) + "px"; 
        	document.getElementById("tooltip").style.top = ( cordY + 20 ) + "px"; 
	        document.getElementById("tooltip").innerHTML = text; 
    	    document.getElementById("tooltip").style.visibility = "visible"; 
 		}
 	}
} 
 
function TX_hideToolTip() { 
        document.getElementById("tooltip").style.visibility = "hidden"; 
        document.getElementById("tooltip").innerHTML = "false"; 
}
