//function silentErrorHandler() {return true;}
//window.onerror=silentErrorHandler;

var isIE    = document.all;

//// Basic Events ////
function BodyOnload(nifty)
{
  // Load Google-Maps, if map-element is found
    if (document.getElementById("GoogleMaps")) {
        ShowMap();
    }

  // Focus on userid field in checkin assistant
    if (document.CheckinAssistantUseridForm) {
        if (document.CheckinAssistantUseridForm.userid) {
            document.CheckinAssistantUseridForm.userid.focus();
        }
    }
}


//// Basic ////

function InsertCode(object, CodeStart, CodeEnd)
{
    if (!CodeEnd) {
        CodeEnd = '';
    }

    if (CodeStart != '') {
        object.focus();

        if (CodeEnd && document.selection) {
            sel = document.selection.createRange();
            selected = sel.text;
            sel.text = CodeStart + selected + CodeEnd;
        } else if (object.selectionStart || object.selectionStart == '0') {
            var startPos = object.selectionStart;
            var endPos = object.selectionEnd;
            object.value = object.value.substring(0, startPos) + CodeStart + object.value.substring(startPos, endPos) + CodeEnd + object.value.substring(endPos, object.value.length);
        } else {
            object.value += CodeStart + CodeEnd;
        }
    }
}

function OpenWindow(url, name)
{
    url = url.replace(/\&amp\;/g, "&");
    Win1 = window.open(url, name, "width=600,height=400,left=100,top=100");
    Win1.focus();
}

function OpenPreviewWindow(url, obj)
{
    var tmp = obj.action;
    obj.action = url;
    Preview = window.open('_blank', 'Preview', "width=600,height=400,left=100,top=100,resizable=yes,scrollbars=yes");
    obj.target = 'Preview';
    obj.submit();
    Preview.focus();

    obj.action = tmp;
    obj.target = '_self';
}

function OpenHelplet(module, helpletid)
{
    w = window.open('index.php?mod=helplet&action=helplet&design=popup&module='+ module +'&helpletid='+ helpletid, 'neu', 'width=800, height=500, resizable=yes, scrollbars=yes');
}

function AddaptTextAreaHeight(obj)
{
//  obj.style.height = 0;
    if (obj.scrollHeight > 100) {
        obj.style.height = obj.scrollHeight + 'px';
    }
}


// OnLoad Alternative
window.onDomReady = initReady;
function initReady(fn)
{
    if (document.addEventListener) {
        document.addEventListener("DOMContentLoaded", fn, false);
    } else {
        document.onreadystatechange = function () {
                readyState(fn)}
    }
}
function readyState(func)
{
    if (document.readyState == "interactive" || document.readyState == "complete") {
        func();
    }
}


//// AJAX ////

// globale Instanz von XMLHttpRequest
var xmlHttp = false;

// XMLHttpRequest-Instanz erstellen
// ... für Internet Explorer
try {
    xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
    try {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (e) {
        xmlHttp = false;
    }
}
// ... für Mozilla, Opera und Safari
if (!xmlHttp && typeof XMLHttpRequest != 'undefined') {
    xmlHttp = new XMLHttpRequest();
}

// aktuelle Daten laden
function loadPage(url)
{
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

function LoadingToolTip(text)
{
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

function changepic(picname, obj)
{
    alert(picname.substr(picname.substr.length - 6, 6));
    if (picname.substr(picname.substr.length - 6, 6) == "none" || picname == "") {
        obj.src = "design/images/transparent.png";
    } else {
        obj.src = picname;
    }
}

function TextAreaPlusCharsLeft(textarea, counter, maxchar)
{
    if (textarea.value.length > maxchar) {
        textarea.value = textarea.value.substr(0, maxchar);
        textarea.blur();
    }
    counter.value = maxchar - textarea.value.length;
}

function CheckBoxBoxActivate(name, id)
{
    if (id) {
        document.getElementById(name).style.display = "";
    } else {
        document.getElementById(name).style.display = "none";
    }
}

function ActivateTab(id)
{
    $("div[name='tabs']").each(function (i) {
        this.style.display = "none";
    });
    $("div#tab"+id).removeAttr("style");

    $("span[name='tablinks']").each(function (i) {
        $(this).removeClass("HeaderMenuItemActive");
        $(this).addClass("HeaderMenuItem");
    });
    $("span#tablink"+id).removeClass("HeaderMenuItem");
    $("span#tablink"+id).addClass("HeaderMenuItemActive");
}

function DropDownBoxActivate(name, id, list)
{
    if (list) {
        var found = false;
        for (var x = 0; x < list.length; x++) {
            if (list[x] == id) {
                found = true;
            }
        }
        if (!found) {
            document.getElementById(name).style.display = "none";
        } else {
            document.getElementById(name).style.display = "";
        }
    } else {
        if (id <= 0) {
            document.getElementById(name).style.display = "none";
        } else {
            document.getElementById(name).style.display = "";
        }

        var Preisliste = document.getElementsByName("price_id")[0];
        if (Preisliste) {
            // Delete all current prices
            for (z = 0; z < Preisliste.length; z++) {
                Preisliste.remove(z);
            }
  
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
                                if (document.all) {
                                    FolgendeOption = Auswahlliste.length;
                                }
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

function CheckPasswordSecurity(password, ImgObj)
{
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
    if (password.length >= 8) {
        TestNumberOfChars = true;
    }

    /* Check for minimum of 2 uppercase Letters */
    if (password.match(/[A-Z].*[A-Z]/)) {
        TestUppercaseChars = true;
    }

    /* Check for minimum of 2 lowercase letters */
    if (password.match(/[a-z].*[a-z]/)) {
        TestLowercaseChars = true;
    }

    /* Check for minimum of 2 digits */
    if (password.match(/[0-9].*[0-9]/)) {
        TestDigits = true;
    }

    /* Check for minimum of 2 special chars */
    var specCharCounter = 0;
    if (password.length > 0) {
        characters = password.split("");

        for (var i = 0; i < characters.length; i++) {
            singleChar = characters[i];
            if (singleChar.match(/[^a-zA-Z0-9]/)) {
                specCharCounter++;
            }
        }

        if (specCharCounter >= 2) {
            TestSpecialChars = true;
        }
    }

    /* Check for whitespaces and ESC-Sequences */
    if (password.match(/\s/)) {
        TestWhiteSpaces = true;
    }

    /* Check for minimum of 2 true ifs */
    if (TestUppercaseChars) {
        counter++;
    }
    if (TestLowercaseChars) {
        counter++;
    }
    if (TestDigits) {
        counter++;
    }
    if (TestSpecialChars) {
        counter++;
    }

    if ((counter >= 2) && (!TestWhiteSpaces)) {
        TestCounter = true;
    }

    if ((counter >= 3) && !(TestWhiteSpaces)) {
        TestCounter2 = true;
    }

    var zaehler = 0;
    if (TestCounter) {
        zaehler++;
    }
    if (TestCounter2) {
        zaehler++;
    }
    if (TestNumberOfChars) {
        zaehler++;
    }

    ImgObj.src = 'design/images/password_bar'+ zaehler +'.jpg';
}


//// Mastersearch2 ////

// Highlights the current table row under the mouse
function markieren(EintragSpalte)
{
    if (typeof(document.getElementsByTagName) != 'undefined') {
        var Spalten = EintragSpalte.getElementsByTagName('td');
    } else if (typeof(EintragSpalte.cells) != 'undefined') {
        var Spalten = EintragSpalte.cells;
    } else {
        return false;
    }

    for (var c = 0; c < Spalten.length; c++) {
        if (Spalten[c].className.substring(Spalten[c].className.length - 9) == 'row_value') {
            Spalten[c].className = Spalten[c].className + '_important';
        }
    }

    return true;
}

// Un-Highlights the current table row under the mouse
function unmarkieren(EintragSpalte)
{
    if (typeof(document.getElementsByTagName) != 'undefined') {
        var Spalten = EintragSpalte.getElementsByTagName('td');
    } else if (typeof(EintragSpalte.cells) != 'undefined') {
        var Spalten = EintragSpalte.cells;
    } else {
        return false;
    }
  
    for (var c = 0; c < Spalten.length; c++) {
        if (Spalten[c].className.substring(Spalten[c].className.length - 19, Spalten[c].className.length) == 'row_value_important') {
            Spalten[c].className = Spalten[c].className.substring(0, Spalten[c].className.length-10);
        }
    }
  
    return true;
}

// Highlights the current table row under the mouse
function markieren_permanent(EintragSpalte)
{
    if (typeof(document.getElementsByTagName) != 'undefined') {
        var Spalten = EintragSpalte.getElementsByTagName('td');
    } else if (typeof(EintragSpalte.cells) != 'undefined') {
        var Spalten = EintragSpalte.cells;
    } else {
        return false;
    }

    for (var c = 0; c < Spalten.length; c++)
    if (Spalten[c].className.substring(Spalten[c].className.length - 9, Spalten[c].className.length) == 'row_value') {
        Spalten[c].className = Spalten[c].className + '_highlighted';
        if (c == 0 && typeof(Spalten[c].getElementsByTagName('input')[0]) != 'undefined') {
            Spalten[c].getElementsByTagName('input')[0].checked = true;
        }
    } else if (Spalten[c].className.substring(Spalten[c].className.length - 19, Spalten[c].className.length) == 'row_value_important') {
        Spalten[c].className = Spalten[c].className.substring(0, Spalten[c].className.length-10) + '_highlighted'
        if (c == 0 && typeof(Spalten[c].getElementsByTagName('input')[0]) != 'undefined') {
            Spalten[c].getElementsByTagName('input')[0].checked = true;
        }
    } else {
        Spalten[c].className = Spalten[c].className.substring(0, Spalten[c].className.length-12);
        if (c == 0 && typeof(Spalten[c].getElementsByTagName('input'[0])) != 'undefined') {
            Spalten[c].getElementsByTagName('input')[0].checked = false;
        }
    }

    return true;
}

// Checks all/none/inverted checkboxes
function change_selection(id, ms_number)
{
    resultObj = eval("document.ms_result" + ms_number);
    if (id.value == "") {
        return 0;
    } else if (id.value == "select_all") {
        for (z=0; z<=resultObj.length-1; z++) {
            resultObj.elements[z].checked = 1;
        } } else if (id.value == "select_none") {
        for (z=0; z<=resultObj.length-1; z++) {
            resultObj.elements[z].checked = 0;
        } } else if (id.value == "select_invert") {
            for (z=0; z<=resultObj.length-1; z++) {
                if (resultObj.elements[z].checked) {
                    resultObj.elements[z].checked = 0;
                } else {
                    resultObj.elements[z].checked = 1;
                }
            } } else {
            var foundOneChecked = 0;
            for (z=0; z<=resultObj.length-1; z++) {
                if (resultObj.elements[z].checked) {
                    foundOneChecked = 1;
                }
            }

            if (!foundOneChecked) {
                alert('Bitte wählen Sie mindestens einen Eintrag aus');
            } else {
                if (MultiSelectSecurityQuest[id.value] == 1) {
                    if (!confirm("Willst du die Aktion '"+ id.options[id.selectedIndex].text +"' wirklich auf alle ausgewählten Einträge anwenden?")) {
                        return 0;
                    }
                }
                MultiSelectActions[id.value] = MultiSelectActions[id.value].replace(/&amp;/g, "&");
                resultObj.action = MultiSelectActions[id.value];
                resultObj.submit();
            }
            }
}


//// Search Box ////
function SubmitDropDown(FormObj)
{
    var pieces = FormObj.SearchBoxModule.value.split("|");
    FormObj.mod.value = pieces[0];
    FormObj.action.value = pieces[1];
    FormObj.SearchBoxModule.value = '';
    FormObj.SearchBoxModule.name = '';
    FormObj.submit();
}



// Tooltip windows
function TX_getScrollPos()
{
    if (document.body.scrollTop != undefined && navigator.appName.indexOf("Explorer") != -1 ) {
        var res = (document.compatMode != "CSS1Compat") ? document.body : document.documentElement;
        return {x : res.scrollLeft, y : res.scrollTop};
    } else {
        return {x : window.pageXOffset, y : window.pageYOffset};
    }
 
}
 

function TX_hideToolTip()
{
        document.getElementById("tooltip").style.visibility = "hidden";
        document.getElementById("tooltip").innerHTML = "false";
}