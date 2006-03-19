  function resizeDialogToContent()
  {
    // resize window so there are no scrollbars visible
    var dw = window.dialogWidth;
    while (isNaN(dw))
    {
      dw = dw.substr(0,dw.length-1);
    }
    difw = dw - this.document.body.clientWidth;
    window.dialogWidth = this.document.body.scrollWidth+difw+'px';

    var dh = window.dialogHeight;
    while (isNaN(dh))
    {
      dh = dh.substr(0,dh.length-1);
    }
    difh = dh - this.document.body.clientHeight;
    window.dialogHeight = this.document.body.scrollHeight+difh+'px';
  }
  
