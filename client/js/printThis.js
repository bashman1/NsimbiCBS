/*
 * printThis v1.14.0
 * @desc Printing plug-in for jQuery
 * @author Jason Day
 */
!(function (S) {
  function b(e, t) {
    t && e.append(t.jquery ? t.clone() : t);
  }
  function g(e, t, n) {
    var i,
      a,
      o,
      r = t.clone(n.formValues);
    n.formValues &&
      ((i = r),
      (a = "select, textarea"),
      (o = t.find(a)),
      i.find(a).each(function (e, t) {
        S(t).val(o.eq(e).val());
      })),
      n.removeScripts && r.find("script").remove(),
      n.printContainer
        ? r.appendTo(e)
        : r.each(function () {
            S(this).children().appendTo(e);
          });
  }
  var C;
  (S.fn.printThis = function (e) {
    C = S.extend({}, S.fn.printThis.defaults, e);
    var y = this instanceof jQuery ? this : S(this),
      t = "printThis-" + new Date().getTime();
    if (
      window.location.hostname !== document.domain &&
      navigator.userAgent.match(/msie/i)
    ) {
      var n =
          'javascript:document.write("<head><script>document.domain=\\"' +
          document.domain +
          '\\";</script></head><body></body>")',
        i = document.createElement("iframe");
      (i.name = "printIframe"),
        (i.id = t),
        (i.className = "MSIE"),
        document.body.appendChild(i),
        (i.src = n);
    } else {
      S("<iframe id='" + t + "' name='printIframe' />").appendTo("body");
    }
    var v = S("#" + t);
    C.debug ||
      v.css({
        position: "absolute",
        width: "0px",
        height: "0px",
        left: "-600px",
        top: "-600px",
      }),
      "function" == typeof C.beforePrint && C.beforePrint(),
      setTimeout(function () {
        var e, t, n, i;
        C.doctypeString &&
          ((e = v),
          (t = C.doctypeString),
          (i =
            (n = (n = e.get(0)).contentWindow || n.contentDocument || n)
              .document ||
            n.contentDocument ||
            n).open(),
          i.write(t),
          i.close());
        var a,
          o = v.contents(),
          r = o.find("head"),
          s = o.find("body"),
          c = S("base");
        (a =
          !0 === C.base && 0 < c.length
            ? c.attr("href")
            : "string" == typeof C.base
            ? C.base
            : document.location.protocol + "//" + document.location.host),
          r.append('<base href="' + a + '">'),
          C.importCSS &&
            S("link[rel=stylesheet]").each(function () {
              var e = S(this).attr("href");
              if (e) {
                var t = S(this).attr("media") || "all";
                r.append(
                  "<link type='text/css' rel='stylesheet' href='" +
                    e +
                    "' media='" +
                    t +
                    "'>"
                );
              }
            }),
          C.importStyle &&
            S("style").each(function () {
              r.append(this.outerHTML);
            }),
          C.pageTitle && r.append("<title>" + C.pageTitle + "</title>"),
          C.loadCSS &&
            (S.isArray(C.loadCSS)
              ? jQuery.each(C.loadCSS, function (e, t) {
                  r.append(
                    "<link type='text/css' rel='stylesheet' href='" +
                      this +
                      "'>"
                  );
                })
              : r.append(
                  "<link type='text/css' rel='stylesheet' href='" +
                    C.loadCSS +
                    "'>"
                ));
        var d = S("html")[0];
        o.find("html").prop("style", d.style.cssText);
        var l,
          p,
          f,
          m = C.copyTagClasses;
        if (
          (m &&
            (-1 !== (m = !0 === m ? "bh" : m).indexOf("b") &&
              s.addClass(S("body")[0].className),
            -1 !== m.indexOf("h") && o.find("html").addClass(d.className)),
          b(s, C.header),
          C.canvas)
        ) {
          var u = 0;
          y.find("canvas")
            .addBack("canvas")
            .each(function () {
              S(this).attr("data-printthis", u++);
            });
        }
        if (
          (g(s, y, C),
          C.canvas &&
            s.find("canvas").each(function () {
              var e = S(this).data("printthis"),
                t = S('[data-printthis="' + e + '"]');
              this.getContext("2d").drawImage(t[0], 0, 0),
                t.removeData("printthis");
            }),
          C.removeInline)
        ) {
          var h = C.removeInlineSelector || "*";
          S.isFunction(S.removeAttr)
            ? s.find(h).removeAttr("style")
            : s.find(h).attr("style", "");
        }
        b(s, C.footer),
          (l = v),
          (p = C.beforePrint),
          (f = (f = l.get(0)).contentWindow || f.contentDocument || f),
          "function" == typeof p &&
            ("matchMedia" in f
              ? f.matchMedia("print").addListener(function (e) {
                  e.matches && p();
                })
              : (f.onbeforeprint = p)),
          setTimeout(function () {
            v.hasClass("MSIE")
              ? (window.frames.printIframe.focus(),
                r.append("<script>  window.print(); </script>"))
              : document.queryCommandSupported("print")
              ? v[0].contentWindow.document.execCommand("print", !1, null)
              : (v[0].contentWindow.focus(), v[0].contentWindow.print()),
              C.debug ||
                setTimeout(function () {
                  v.remove();
                }, 1e3),
              "function" == typeof C.afterPrint && C.afterPrint();
          }, C.printDelay);
      }, 333);
  }),
    (S.fn.printThis.defaults = {
      debug: !1,
      importCSS: !0,
      importStyle: !1,
      printContainer: !0,
      loadCSS: "",
      pageTitle: "",
      removeInline: !1,
      removeInlineSelector: "*",
      printDelay: 333,
      header: null,
      footer: null,
      base: !1,
      formValues: !0,
      canvas: !1,
      doctypeString: "<!DOCTYPE html>",
      removeScripts: !1,
      copyTagClasses: !1,
      beforePrintEvent: null,
      beforePrint: null,
      afterPrint: null,
    });
})(jQuery);
