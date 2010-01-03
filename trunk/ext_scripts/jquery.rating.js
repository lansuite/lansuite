(function($) {
  var buildRating = function(obj) {
    var rating = averageRating(obj),
        obj    = buildInterface(obj),
        stars  = $("div.star", obj),
        cancel = $("div.cancel", obj)

        var fill = function() {
          drain();
          $("a", stars).css("width", "100%");
          for(var x = 0; x <= stars.index(this); x++) stars.eq(x).addClass("hover");
        },
        drain = function() {
          stars.removeClass("on").removeClass("hover");
        },
        reset = function() {
          drain();
          for(var x = 0; x < rating[0]; x++) stars.eq(x).addClass("on");
          if(percent = rating[1] ? rating[1] * 10 : null) {
            stars.eq(rating[0]).addClass("on").children("a").css("width", percent + "%");
          }
        },
        cancelOn = function() {
          drain();
          $(this).addClass("on");
        },
        cancelOff = function() {
          reset();
          $(this).removeClass("on")
        }

    stars
      .hover(fill, reset).focus(fill).blur(reset)
      .click(function() {
        rating = [stars.index(this) + 1, 0];
        $.post(obj.url, { rating: $("a:first", this)[0].href.slice($("a:first", this)[0].href.indexOf("#") + 1) });
        reset(); stars.unbind().addClass("done");
        $(this).css("cursor", "default");
        return false;
      });

    reset();
    return obj;

  }

  var buildInterface = function(form) {
    var container = $("<div></div>").attr({"title": form.title, "class": form.className});
    $.extend(container, {url: form.action})
    var optGroup  = $("option", $(form));
    var size      = optGroup.length;
    optGroup.each(function() {
      container.append($('<div class="star"><a href="#' + this.value + '" title="Give it ' + this.value + '/'+ size +'">' + this.value + '</a></div>'));
    });
    $(form).after(container).remove();
    return container;
  }

  var averageRating = function(el) { return el.title.split(":")[1].split(".") }

  $.fn.rating = function() { return $($.map(this, function(i) { return buildRating(i)[0] })); }

	if ($.browser.msie) try { document.execCommand("BackgroundImageCache", false, true)} catch(e) { }

})(jQuery)