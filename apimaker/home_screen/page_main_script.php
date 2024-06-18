<script type="text/javascript">
/*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
(function(w) {
    "use strict";
    if (!w.loadCSS) {
        w.loadCSS = function() {}
    }
    var rp = loadCSS.relpreload = {};
    rp.support = (function() {
        var ret;
        try {
            ret = w.document.createElement("link").relList.supports("preload")
        } catch (e) {
            ret = !1
        }
        return function() {
            return ret
        }
    })();
    rp.bindMediaToggle = function(link) {
        var finalMedia = link.media || "all";

        function enableStylesheet() {
            link.media = finalMedia
        }
        if (link.addEventListener) {
            link.addEventListener("load", enableStylesheet)
        } else if (link.attachEvent) {
            link.attachEvent("onload", enableStylesheet)
        }
        setTimeout(function() {
            link.rel = "stylesheet";
            link.media = "only x"
        });
        setTimeout(enableStylesheet, 3000)
    };
    rp.poly = function() {
        if (rp.support()) {
            return
        }
        var links = w.document.getElementsByTagName("link");
        for (var i = 0; i < links.length; i++) {
            var link = links[i];
            if (link.rel === "preload" && link.getAttribute("as") === "style" && !link.getAttribute("data-loadcss")) {
                link.setAttribute("data-loadcss", !0);
                rp.bindMediaToggle(link)
            }
        }
    };
    if (!rp.support()) {
        rp.poly();
        var run = w.setInterval(rp.poly, 500);
        if (w.addEventListener) {
            w.addEventListener("load", function() {
                rp.poly();
                w.clearInterval(run)
            })
        } else if (w.attachEvent) {
            w.attachEvent("onload", function() {
                rp.poly();
                w.clearInterval(run)
            })
        }
    }
    if (typeof exports !== "undefined") {
        exports.loadCSS = loadCSS
    } else {
        w.loadCSS = loadCSS
    }
}(typeof global !== "undefined" ? global : this))



/* <![CDATA[ */
var php_data = {
    "ac_settings": {
        "tracking_actid": 27678010,
        "site_tracking_default": 1,
        "site_tracking": 1
    },
    "user_email": ""
};
/* ]]> */

/* <![CDATA[ */
var wpcf7 = {
    "apiSettings": {
        "root": "https:\/\/backendless.com\/wp-json\/contact-form-7\/v1",
        "namespace": "contact-form-7\/v1"
    },
    "cached": "1"
};
/* ]]> */

/* <![CDATA[ */
var qpprFrontData = {
    "linkData": {
        "\/video-tutorial\/part-2-backendless-api-engine-demo\/": [1, 1, ""],
        "\/video-tutorial\/part-1-introduction-to-backendless-api-engine\/": [1, 1, ""],
        "\/video-tutorial\/how-to-remove-data-relations-with-backendless-rest\/": [1, 1, ""],
        "\/video-tutorial\/file-download-with-user-authentication\/": [1, 1, ""],
        "\/video-tutorial\/running-custom-logic-when-data-objects-change\/": [1, 1, ""],
        "\/video-tutorial\/how-to-turn-java-code-into-a-service-with-apis\/": [1, 1, ""],
        "\/video-tutorial\/setting-up-email-and-configuring-templates\/": [1, 1, ""],
        "\/video-tutorial\/managing-database-schema-using-console\/": [1, 1, ""],
        "\/video-tutorial\/exporting-application-data-in-backendless\/": [1, 1, ""],
        "\/video-tutorial\/sharing-mbaas-backend-with-a-development-team\/": [1, 1, ""],
        "\/video-tutorial\/managing-app-users-in-backendless\/": [1, 1, ""],
        "\/video-tutorial\/backendless-and-facebook-login-integration\/": [1, 1, ""],
        "\/video-tutorial\/user-registration-and-login\/": [1, 1, ""],
        "\/video-tutorial\/video-1\/": [1, 1, ""],
        "\/support\/video-tutorials\/": [1, 1, ""],
        "\/knowledge-base\/": [1, 1, ""],
        "\/backend-as-a-service\/video-tutorials\/": [1, 1, ""]
    },
    "siteURL": "https:\/\/backendless.com",
    "siteURLq": "https:\/\/backendless.com"
};
/* ]]> */

"use strict";
var _createClass = function() {
    function defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || !1, descriptor.configurable = !0, "value" in descriptor && (descriptor.writable = !0), Object.defineProperty(target, descriptor.key, descriptor)
        }
    }
    return function(Constructor, protoProps, staticProps) {
        return protoProps && defineProperties(Constructor.prototype, protoProps), staticProps && defineProperties(Constructor, staticProps), Constructor
    }
}();

function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) throw new TypeError("Cannot call a class as a function")
}
var RocketBrowserCompatibilityChecker = function() {
    function RocketBrowserCompatibilityChecker(options) {
        _classCallCheck(this, RocketBrowserCompatibilityChecker), this.passiveSupported = !1, this._checkPassiveOption(this), this.options = !!this.passiveSupported && options
    }
    return _createClass(RocketBrowserCompatibilityChecker, [{
        key: "_checkPassiveOption",
        value: function(self) {
            try {
                var options = {
                    get passive() {
                        return !(self.passiveSupported = !0)
                    }
                };
                window.addEventListener("test", null, options), window.removeEventListener("test", null, options)
            } catch (err) {
                self.passiveSupported = !1
            }
        }
    }, {
        key: "initRequestIdleCallback",
        value: function() {
            !1 in window && (window.requestIdleCallback = function(cb) {
                var start = Date.now();
                return setTimeout(function() {
                    cb({
                        didTimeout: !1,
                        timeRemaining: function() {
                            return Math.max(0, 50 - (Date.now() - start))
                        }
                    })
                }, 1)
            }), !1 in window && (window.cancelIdleCallback = function(id) {
                return clearTimeout(id)
            })
        }
    }, {
        key: "isDataSaverModeOn",
        value: function() {
            return "connection" in navigator && !0 === navigator.connection.saveData
        }
    }, {
        key: "supportsLinkPrefetch",
        value: function() {
            var elem = document.createElement("link");
            return elem.relList && elem.relList.supports && elem.relList.supports("prefetch") && window.IntersectionObserver && "isIntersecting" in IntersectionObserverEntry.prototype
        }
    }, {
        key: "isSlowConnection",
        value: function() {
            return "connection" in navigator && "effectiveType" in navigator.connection && ("2g" === navigator.connection.effectiveType || "slow-2g" === navigator.connection.effectiveType)
        }
    }]), RocketBrowserCompatibilityChecker
}();

/* <![CDATA[ */
var RocketPreloadLinksConfig = {
    "excludeUris": "\/support\/|\/(.+\/)?feed\/?.+\/?|\/(?:.+\/)?embed\/|\/(index\\.php\/)?wp\\-json(\/.*|$)|\/wp-admin\/|\/logout\/|\/aghsdf2sd\/",
    "usesTrailingSlash": "1",
    "imageExt": "jpg|jpeg|gif|png|tiff|bmp|webp|avif",
    "fileExt": "jpg|jpeg|gif|png|tiff|bmp|webp|avif|php|pdf|html|htm",
    "siteUrl": "https:\/\/backendless.com",
    "onHoverDelay": "100",
    "rateThrottle": "3"
};
/* ]]> */

(function() {
    "use strict";
    var r = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(e) {
            return typeof e
        } : function(e) {
            return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
        },
        e = function() {
            function i(e, t) {
                for (var n = 0; n < t.length; n++) {
                    var i = t[n];
                    i.enumerable = i.enumerable || !1, i.configurable = !0, "value" in i && (i.writable = !0), Object.defineProperty(e, i.key, i)
                }
            }
            return function(e, t, n) {
                return t && i(e.prototype, t), n && i(e, n), e
            }
        }();

    function i(e, t) {
        if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
    }
    var t = function() {
        function n(e, t) {
            i(this, n), this.browser = e, this.config = t, this.options = this.browser.options, this.prefetched = new Set, this.eventTime = null, this.threshold = 1111, this.numOnHover = 0
        }
        return e(n, [{
            key: "init",
            value: function() {
                !this.browser.supportsLinkPrefetch() || this.browser.isDataSaverModeOn() || this.browser.isSlowConnection() || (this.regex = {
                    excludeUris: RegExp(this.config.excludeUris, "i"),
                    images: RegExp(".(" + this.config.imageExt + ")$", "i"),
                    fileExt: RegExp(".(" + this.config.fileExt + ")$", "i")
                }, this._initListeners(this))
            }
        }, {
            key: "_initListeners",
            value: function(e) {
                -1 < this.config.onHoverDelay && document.addEventListener("mouseover", e.listener.bind(e), e.listenerOptions), document.addEventListener("mousedown", e.listener.bind(e), e.listenerOptions), document.addEventListener("touchstart", e.listener.bind(e), e.listenerOptions)
            }
        }, {
            key: "listener",
            value: function(e) {
                var t = e.target.closest("a"),
                    n = this._prepareUrl(t);
                if (null !== n) switch (e.type) {
                    case "mousedown":
                    case "touchstart":
                        this._addPrefetchLink(n);
                        break;
                    case "mouseover":
                        this._earlyPrefetch(t, n, "mouseout")
                }
            }
        }, {
            key: "_earlyPrefetch",
            value: function(t, e, n) {
                var i = this,
                    r = setTimeout(function() {
                        if (r = null, 0 === i.numOnHover) setTimeout(function() {
                            return i.numOnHover = 0
                        }, 1e3);
                        else if (i.numOnHover > i.config.rateThrottle) return;
                        i.numOnHover++, i._addPrefetchLink(e)
                    }, this.config.onHoverDelay);
                t.addEventListener(n, function e() {
                    t.removeEventListener(n, e, {
                        passive: !0
                    }), null !== r && (clearTimeout(r), r = null)
                }, {
                    passive: !0
                })
            }
        }, {
            key: "_addPrefetchLink",
            value: function(i) {
                return this.prefetched.add(i.href), new Promise(function(e, t) {
                    var n = document.createElement("link");
                    n.rel = "prefetch", n.href = i.href, n.onload = e, n.onerror = t, document.head.appendChild(n)
                }).catch(function() {})
            }
        }, {
            key: "_prepareUrl",
            value: function(e) {
                if (null === e || "object" !== (void 0 === e ? "undefined" : r(e)) || !1 in e || -1 === ["http:", "https:"].indexOf(e.protocol)) return null;
                var t = e.href.substring(0, this.config.siteUrl.length),
                    n = this._getPathname(e.href, t),
                    i = {
                        original: e.href,
                        protocol: e.protocol,
                        origin: t,
                        pathname: n,
                        href: t + n
                    };
                return this._isLinkOk(i) ? i : null
            }
        }, {
            key: "_getPathname",
            value: function(e, t) {
                var n = t ? e.substring(this.config.siteUrl.length) : e;
                return n.startsWith("/") || (n = "/" + n), this._shouldAddTrailingSlash(n) ? n + "/" : n
            }
        }, {
            key: "_shouldAddTrailingSlash",
            value: function(e) {
                return this.config.usesTrailingSlash && !e.endsWith("/") && !this.regex.fileExt.test(e)
            }
        }, {
            key: "_isLinkOk",
            value: function(e) {
                return null !== e && "object" === (void 0 === e ? "undefined" : r(e)) && (!this.prefetched.has(e.href) && e.origin === this.config.siteUrl && -1 === e.href.indexOf("?") && -1 === e.href.indexOf("#") && !this.regex.excludeUris.test(e.href) && !this.regex.images.test(e.href))
            }
        }], [{
            key: "run",
            value: function() {
                "undefined" != typeof RocketPreloadLinksConfig && new n(new RocketBrowserCompatibilityChecker({
                    capture: !0,
                    passive: !0
                }), RocketPreloadLinksConfig).init()
            }
        }]), n
    }();
    t.run();
}());

var fb_timeout, fb_opts = {
    'overlayShow': true,
    'hideOnOverlayClick': true,
    'overlayOpacity': 0.9,
    'showCloseButton': true,
    'width': 640,
    'margin': 20,
    'centerOnScroll': true,
    'enableEscapeButton': true,
    'autoScale': true
};
if (typeof easy_fancybox_handler === 'undefined') {
    var easy_fancybox_handler = function() {
        jQuery([".nolightbox", "a.wp-block-file__button", "a.pin-it-button", "a[href*='pinterest.com\/pin\/create']", "a[href*='facebook.com\/share']", "a[href*='twitter.com\/share']"].join(',')).addClass('nofancybox');
        jQuery('a.fancybox-close').on('click', function(e) {
            e.preventDefault();
            jQuery.fancybox.close()
        });
        /* IMG */
        var fb_IMG_select = 'a[href*=".jpg"]:not(.nofancybox,li.nofancybox>a),area[href*=".jpg"]:not(.nofancybox),a[href*=".jpeg"]:not(.nofancybox,li.nofancybox>a),area[href*=".jpeg"]:not(.nofancybox),a[href*=".png"]:not(.nofancybox,li.nofancybox>a),area[href*=".png"]:not(.nofancybox),a[href*=".webp"]:not(.nofancybox,li.nofancybox>a),area[href*=".webp"]:not(.nofancybox)';
        jQuery(fb_IMG_select).addClass('fancybox image');
        var fb_IMG_sections = jQuery('.gallery,.wp-block-gallery,.tiled-gallery,.wp-block-jetpack-tiled-gallery');
        fb_IMG_sections.each(function() {
            jQuery(this).find(fb_IMG_select).attr('rel', 'gallery-' + fb_IMG_sections.index(this));
        });
        jQuery('a.fancybox,area.fancybox,li.fancybox a').each(function() {
            jQuery(this).fancybox(jQuery.extend({}, fb_opts, {
                'transitionIn': 'elastic',
                'easingIn': 'easeOutBack',
                'transitionOut': 'elastic',
                'easingOut': 'easeInBack',
                'opacity': false,
                'hideOnContentClick': false,
                'titleShow': false,
                'titlePosition': 'over',
                'titleFromAlt': true,
                'showNavArrows': true,
                'enableKeyboardNav': true,
                'cyclic': false
            }))
        });
        /* Inline */
        jQuery('a.fancybox-inline,area.fancybox-inline,li.fancybox-inline a').each(function() {
            jQuery(this).fancybox(jQuery.extend({}, fb_opts, {
                'type': 'inline',
                'autoDimensions': true,
                'scrolling': 'no',
                'easingIn': 'easeOutBack',
                'easingOut': 'easeInBack',
                'opacity': false,
                'hideOnContentClick': false,
                'titleShow': false
            }))
        });
    };
};
var easy_fancybox_auto = function() {
    setTimeout(function() {
        jQuery('#fancybox-auto').trigger('click')
    }, 1000);
};
jQuery(easy_fancybox_handler);
jQuery(document).on('post-load', easy_fancybox_handler);
jQuery(easy_fancybox_auto);

var Ajax = {
    "URL": "https:\/\/backendless.com\/wp-admin\/admin-ajax.php"
};

setTimeout(function() {
    (function(d, u, id, i) {
        u = 'https://www.googletagmanager.com/gtag/js?id=UA-37519430-1';
        i = document.createElement('script');
        i.type = 'application/javascript';
        i.async = true;
        i.src = u;
        d.getElementsByTagName('head')[0].appendChild(i);
    }(document));
}, 5000);

window.dataLayer = window.dataLayer || [];

function gtag() {
    dataLayer.push(arguments);
}
gtag('js', new Date());

gtag('config', 'UA-37519430-1');

setTimeout(function() {
    (function(c, p, d, u, id, i) {
        id = ''; // Optional Custom ID for user in your system
        u = 'https://tracking.g2crowd.com/attribution_tracking/conversions/' + c + '.js?p=' + encodeURI(p) + '&e=' + id;
        i = document.createElement('script');
        i.type = 'application/javascript';
        i.async = true;
        i.src = u;
        d.getElementsByTagName('head')[0].appendChild(i);
    }("1007014", document.location.href, document));
}, 5000);

(function(w) {
    w.fpr = w.fpr || function() {
        w.fpr.q = w.fpr.q || [];
        w.fpr.q[arguments[0] == 'set' ? 'unshift' : 'push'](arguments);
    };
})(window);
fpr("init", {
    cid: "2ewklfyc"
});
fpr("click");

"use strict";
var wprRemoveCPCSS = function wprRemoveCPCSS() {
    var elem;
    document.querySelector('link[data-rocket-async="style"][rel="preload"]') ? setTimeout(wprRemoveCPCSS, 200) : (elem = document.getElementById("rocket-critical-css")) && "remove" in elem && elem.remove()
};
window.addEventListener ? window.addEventListener("load", wprRemoveCPCSS) : window.attachEvent && window.attachEvent("onload", wprRemoveCPCSS);
</script>