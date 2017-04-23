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
 
function TX_showToolTip(e, data_array)
{
    if (data_array != 'null') {
        var scr = TX_getScrollPos();
        var cordX = e.clientX + scr.x;
        var cordY = e.clientY + scr.y;

        var text = '<table>';
        text += '<tr><td style="font-weight: bold;">Ort:</td><td style="white-space:nowrap;">'+ data_array[0] +' '+ data_array[1] +'</td></tr>';
        text += '<tr><td style="font-weight: bold;">Besucherzahl:</td><td>'+ data_array[2] +'</td></tr>';
        for (var i = 3; i < data_array.length; i = i + 3) {
            text += '<tr><td style="font-weight: bold;">Besucher:</td><td style="white-space:nowrap;">'+ data_array[i] +' ('+ data_array[i+1] +' '+ data_array[i+2] +')</td></tr>';
        }
        text += '</table>';

        document.getElementById("tooltip").style.position = "absolute";
        document.getElementById("tooltip").style.left =  ( cordX + 20 ) + "px";
        document.getElementById("tooltip").style.top = ( cordY + 20 ) + "px";
        document.getElementById("tooltip").innerHTML = text;
        document.getElementById("tooltip").style.visibility = "visible";
    }
}
 
function TX_hideToolTip()
{
        document.getElementById("tooltip").style.visibility = "hidden";
        document.getElementById("tooltip").innerHTML = "false";
} 