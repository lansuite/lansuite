function CreateRect(x, y, width, height, fill, stroke, link) {
	myRoundRect = vectorModel.createElement("rect");
	myRoundRect.setAttribute("x", x);
	myRoundRect.setAttribute("y", y);
	myRoundRect.setAttribute("z-index", 1);
	myRoundRect.setAttribute("width", width);
	myRoundRect.setAttribute("height", height);
	myRoundRect.setAttribute("stroke", stroke);
	myRoundRect.setAttribute("stroke-width", "1px");
	myRoundRect.setAttribute("fill", fill);
	myRoundRect.setAttribute("rx", "5");
	myRoundRect.setAttribute("ry", "5");
  if (link) {
    myRoundRect.setAttribute("onclick", "parent.document.location.href='"+ link +"'");
    myRoundRect.setAttribute("onmouseover", "this.style.cursor='pointer'");
  }
	myG.appendChild(myRoundRect);
}

function CreateSmallRect(x, y, width, height, fill, link, popup) {
	myRoundRect = vectorModel.createElement("rect");
	myRoundRect.setAttribute("x", x);
	myRoundRect.setAttribute("y", y);
	myRoundRect.setAttribute("z-index", 1);
	myRoundRect.setAttribute("width", width);
	myRoundRect.setAttribute("height", height);
	//myRoundRect.setAttribute("stroke", stroke);
	//myRoundRect.setAttribute("stroke-width", "0px");
	myRoundRect.setAttribute("fill", fill);
	myRoundRect.setAttribute("rx", "2");
	myRoundRect.setAttribute("ry", "2");
  if (link) {
    myRoundRect.setAttribute("onclick", "parent.document.location.href='"+ link +"'");
    myRoundRect.setAttribute("onmouseover", "this.style.cursor='pointer'");
  }
  if (popup) {
    myRoundRect.setAttribute("onmousemove", "return overlib('"+ popup +"')");
    myRoundRect.setAttribute("onmouseout", "return nd();");
  }
	myG.appendChild(myRoundRect);
}

function CreateText(text, x, y, link) {
  var myText = document.createTextNode(text);
  var myT = vectorModel.createElement("text");
  if (myT && text != '_') {
    myT.setAttribute("x", x);
    myT.setAttribute("y", y);
    if (link) {
      myT.setAttribute("onclick", "parent.document.location.href='"+ link +"'");
      myT.setAttribute("onmouseover", "this.style.cursor='pointer'");
    }
    myT.setAttribute("style", "font-family:verdana; font-size:8pt;");
    myT.appendChild(myText);
    myG.appendChild(myT);
  } else {
    var myDiv = document.createElement("div");

    if (link) {
      var myA = document.createElement("a");
      myA.setAttribute("href", link);
      myA.setAttribute("target", "_parent");
      myA.style.color="#000000";
      myA.style.textDecoration="none";
      myA.appendChild(myText);
      myDiv.appendChild(myA);
    } else {
      myDiv.appendChild(myText);
    }
    myDiv.style.fontFamily="verdana";
    myDiv.style.fontSize="8pt";
    myDiv.style.margin="1pt";
    myDiv.style.position="absolute";
    myDiv.style.left=x;
    myDiv.style.top=y-12;
    myDiv.style.width="140px";
    myDiv.style.zIndex=999;
    myG.appendChild(myDiv);
  }
}

function CreateLine(x1, y1, x2, y2, stroke) { 
	var myLine = document.createElementNS("http://www.w3.org/2000/svg", "line");
	myLine.setAttribute("x1", x1);
	myLine.setAttribute("y1", y1);
	myLine.setAttribute("x2", x2);
	myLine.setAttribute("y2", y2);
	myLine.setAttribute("stroke", stroke);
	myLine.setAttribute("stroke-width", "1");
	myG.appendChild(myLine);
}

function CreateThickLine(x1, y1, x2, y2, stroke) { 
	var myLine = document.createElementNS("http://www.w3.org/2000/svg", "line");
	myLine.setAttribute("x1", x1);
	myLine.setAttribute("y1", y1);
	myLine.setAttribute("x2", x2);
	myLine.setAttribute("y2", y2);
	myLine.setAttribute("stroke", stroke);
	myLine.setAttribute("stroke-width", "4");
	myG.appendChild(myLine);
}