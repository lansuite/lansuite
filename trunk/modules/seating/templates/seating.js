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
var icon_nr = 0;

function changeImage(id){
	var i = 0;
	if(mouseflag == 1){
		while(document.getElementsByName("icon")[i]){
			if(document.getElementsByName("icon")[i].checked){
				img = document.getElementsByName("icon")[i].value;
				icon_nr = i;
			}
			i++;
		}
		img = img.substring(0,img.length -4);
		if( id != 'null' && document.getElementById(id.id.substring(1,id.id.length)).value != img){
			id.style.background = "transparent url(" + image[img].src + ") repeat scroll 0% 0%";
			document.getElementById(id.id.substring(1,id.id.length)).value = img;
		}
	}
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

function AllselectH(rowid) {
	for (var x = 0; x < cols; x++) {
		var zahl = x * 100 + rowid;
		if (document.getElementById("cell"+zahl)) document.getElementById("cell"+zahl).checked = flagH[rowid];
	}
	flagH[rowid] = !flagH[rowid];
}


function AllselectV(colid) {
	for (var x = 0; x < rows; x++) { 	
		var zahl = colid * 100 + x;
		if (document.getElementById("cell"+zahl)) document.getElementById("cell"+zahl).checked = flagV[colid];
	}
	flagV[colid] = !flagV[colid];
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
        	break;
        
// Änderung durch HSE: 3 Zeilen eingefügt
        case "7":
        	text += '<tr><td style="font-weight: bold;">Block :</td><td>' + data_array[4] + " Gesperrt</td></tr>";
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