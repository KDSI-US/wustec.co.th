var kdsi_piu = function(b) {
	function c(a, b) {
		if(void 0 !== b && void 0 !== t[a]) {
			if(void 0 !== t[a][b]) return t[a][b];
		} else if(void 0 !== t[a]) return t[a];
		return b || a
	}

	function d(a, b, c) {
		void 0 !== c && void 0 !== b && void 0 !== a ? void 0 !== t[a] && (t[a][b] = c) : void 0 !== b && void 0 !== a && (t[a] = b)
	}

	function e() {
		return s = b("#list-view .image").length, s < c("content", "max_images") ? void(y.attr("style") ? y.html(c("lang", "dad_an_image")).attr("style", "") : "") : (b("#drop-files p").html(c("lang", "u_up_max") + " " + c("content", "max_images") + " " + c("lang", "images") + "!").css("color", "#fd8b8b"), !0)
	}

	function f(a, b) {
		var d = "";
		if(a = a.replace(/(\#|\%|\&| )/g, "-"), "default" === b) {
			var e = 175 - a.length;
			return d = 0 > e ? a.slice(0, e) : a, d
		}
		var f = c("content", "translit_map");
		if("random" === b) {
			for(var g = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", h = 0; h < a.length + 25 && !(175 < h); h++) d += g.charAt(Math.floor(Math.random() * g.length));
			return d
		}
		if(/[^\u0000-\u007f]/.test(a) && /\{.\}/.test(f)) {
			for(var j, k = "", h = 0; h < a.length && !(175 < h); h++) k = "", 0 > "~!@$^()_-+={}][".indexOf(a[h]) && (k = new RegExp("(\\w+)\\{(\\p{L}|\\d)*" + a[h] + "(\\p{L}|\\d)*\\}", "ui")), d += (j = f.match(k)) && j[1] ? j[1] : /[\w-()]/.test(a[h]) ? a[h] : "-";
			return d
		}
		return a
	}

	function g() {
		var a = c("content", "upload_folder");
		a = a.replace(/\{(.*?)\}/g, function(a, d) {
			if("year" === d || "month" === d || "day" === d) return c(d);
			if("manufacturer" === d) {
				var e = b("[name ^=" + d + "]");
				if(e[1] && 0 != e[1].value) return e[0].value;
				if(0 != e[0].value && "SELECT" === e[0].tagName) return e[0].querySelector("option:checked").text
			} else try {
				if(val = b("[name =" + d + "]").val()) return val.toLowerCase()
			} catch(a) {}
			return "undefined"
		}), a = a.split("|");
		for(var d = "translit" == c("content", "image_name") && c("content", "translit_map"), e = 0; e < a.length; e++)
			if(/[^\u0000-\u007f]/.test(a[e]) && (d ? a[e] = f(a[e], d) : a[e] = "undefined"), "undefined" != a[e]) {
				a = a[e] ? a[e] + "/" : "";
				break
			} else a.length === e + 1 && (a = "");
		return(r ? "catalog/" : "data/") + a.replace(/(\#|\%|\&| )/g, "-")
	}

	function h(a, b, c, d, e, f, g) {
		if(void 0 === f ? f = .99 : null, "original" === b && .99 <= f && !e) return g;
		var i = c.width,
			j = c.height,
			k = document.createElement("canvas");
		"original" !== b && c.height > b && (i = c.width * b / c.height, j = b), k.width = i, k.height = j;
		var l = k.getContext("2d");
		l.drawImage(c, 0, 0, i, j), /blob:|wm_bg|base64/.test(c.src) && e && q({
			context: l,
			width: i,
			height: j
		}, e);
		var m = "image/" + ("jpg" == a ? "jpeg" : a),
			n = d ? k : k.toDataURL(m, f);
		if(g && 1.01 * g.size < 3 * n.length / 4) {
			var o = k.toDataURL(m, 0).length;
			n = k.toDataURL(m, 1 - o / g.size)
		}
		return n
	}

	function i(d) {
		if(0 != d.length && v && !(s >= c("content", "max_images"))) {
			v = !1;
			var f = [],
				h = {
					imageName: c("content", "image_name"),
					quality: (100 - c("content", "image_quality") + 1) % 101 / 100 % 1 || .99,
					size: u[c("content", "image_size")]
				};
			a && s || (a = g(), b("#list-view>input.img-input").remove());
			for(var k = 0; k < d.length; k++) {
				if(/(\.(jpe?g|png|gif|bmp)$)/i.test(d[k].name || d[k])) {
					var l = b("<div class =\"loaderImg image\" ><div class=\"loading\"><div></div><span>" + c("lang", "loading") + "...</span></div></div>");
					d[k].src || "string" == typeof d[k] ? f.push({
						imageBlock: l,
						url: d[k].src || d[k],
						name: d[k].name || d[k].replace(/.*\//, "")
					}) : (d[k].imageBlock = l, f.push(d[k])), b("#list-view").append(document.createTextNode("\n")), b("#list-view").append(l)
				}
				if(e()) break
			}
			j(f, h)
		}
	}

	function j(b, d) {
		if(0 == b.length) return void(v = !0);
		var e = new Image,
			g = b.shift(),
			i = "new",
			l = g.imageBlock,
			m = g.name.split(/\.(?=[^.]*$)/),
			o = /png|gif/i.test(m[1]) ? m[1] : "jpg",
			p = f(m[0], d.imageName) + "." + o,
			q = a + p;
		e.onerror = function() {
			n("  \"" + p + "\" - " + c("lang", "image_damaged"), "danger"), l.remove(), j(b, d)
		}, e.onload = function() {
			if("new" == i) {
				var a = "on" === c("content", "watermark_status") && c("content", "watermark");
				"random" !== d.imageName && (k(o, l, q), A++), l.data("upl", {
					name: p,
					type: o,
					value: h(o, d.size, this, !1, a, d.quality, g)
				})
			}
			var e = h(o, 200, this, !1, a, d.quality);
			l.html("<span class='previewImg'></span>").removeClass("loaderImg").addClass(i), l.find(".previewImg").append("<img src='" + e + "' />");
			var f = "<span  class=\"btm_img \">";
			f += "<i class=\"fa fa-search-plus\" data-toggle=\"tooltip\" aria-hidden=\"true\" data-original-title =\"" + c("lang", "zoom") + "\" ></i>", f += "<i class=\"fa fa-times\" data-toggle=\"tooltip\" aria-hidden=\"true\" data-original-title =\"" + c("lang", "delete") + "\" ></i>", f += "<i class=\"fa fa-del-all fa-flip-horizontal\" data-toggle=\"tooltip\" aria-hidden=\"true\" data-original-title =\"" + c("lang", "delete_all") + "\" ></i>", f += "</span>", f += "<input " + (l.prev().length ? "" : "id=\"input-image\"") + " value=\"" + q + "\" class = \"img-input\" type=\"hidden\" >", l.append(f), B.addimage && B.addimage.forEach(function(a) {
				"function" == typeof a && a(l, b.length)
			}), window.poipInstalled && poip.product_options.length && poip.showImages(l, b.length), setTimeout(function() {
				j(b, d)
			}, 20)
		}, g.type ? (window.URL = window.URL || window.webkitURL, e.src = window.URL.createObjectURL(g)) : (e.src = g.url, -1 != g.url.indexOf(z) && (e.src = g.url, i = "old", q = g.url.replace(z, "")))
	}

	function k(a, d, e) {
		b.ajax({
			url: "index.php?route=catalog/product/imageExist" + c("token"),
			type: "POST",
			contentType: "application/x-www-form-urlencoded",
			data: {
				name: e
			},
			success: function(e) {
				if(0 != e.status) {
					var f = new Image,
						g = d.find(".img-input").val(),
						i = document.createElement("div");
					i.className = "aExist";
					var j = "<img src=\"\" class=\"ui-sortable-handle\" ><p class=\"e_text\">" + c("lang", "exist") + "</p>";
					j += "<p class=\"e_bottom\"><button id=\"keep\" type=\"button\" class=\"btn btn-default keep_both\">" + c("lang", "keep_both") + "</button>", j += "<button id=\"rep\" type=\"button\" class=\"btn btn-default\">" + c("lang", "replace") + "</button>", j += "<button id=\"skip\" type=\"button\" class=\"btn btn-default\">" + c("lang", "keep_old") + "</button></p>", i.innerHTML = j, d.append(i), f.onload = function() {
						b(i).show("fast"), b("img", i).attr("src", h(a, 200, this, !1))
					}, f.src = z + g, b("button", i).one("click", function() {
						b(i).hide("fast"), "skip" == this.id ? d.removeData("upl").find(".previewImg").html("<img src='" + h(a, 200, f, !1) + "' />") : "keep" == this.id && (d.data("upl").name = e.newname, d.find(".img-input").val(g.match(/.*\//) + e.newname)), setTimeout(function() {
							b(i).remove()
						}, 200)
					})
				}
				A--
			}
		})
	}

	function l(d) {
		var e = {},
			f = new FormData;
		if(b("#list-view .new").each(function(a) {
				var c = b(this).data("upl");
				if(c && !e[c.name] && c.type) {
					if("object" == typeof c.value) var d = c.value;
					else {
						for(var g = atob(c.value.split(",")[1]), h = [], j = 0; j < g.length; j++) h.push(g.charCodeAt(j));
						var d = new Blob([new Uint8Array(h)], {
							type: "image/" + c.type
						})
					}
					f.append(a, d, c.name), e[c.name] = !0, e[0] ? null : e[0] = "image"
				}
				this.className = this.className.replace("new", "old"), b(this).removeData("upl")
			}), w.length) {
			for(var g = b("[id^=input-description], .old .img-input").map(function() {
					return this.value.match(new RegExp(z + "(.*?.(jpe?g|png|gif|bmp))", "g")) || this.value
				}), h = 0; h < w.length; h++) - 1 == b.inArray(w[h], g) && (f.append("dels[value][]", w[h]), e[0] && "image" !== e[0] || (f.append("dels[id]", c("product_id")), e[0] = e[0] ? "image + delete" : "delete"));
			w = []
		}
		f.append("catalog", a), e[0] ? b.ajax({
			async: !0,
			contentType: !1,
			data: f,
			processData: !1,
			type: "post",
			url: "index.php?route=catalog/product/uploadNewImage" + c("token"),
			xhr: function() {
				var a = b.ajaxSettings.xhr();
				if(-1 < e[0].indexOf("image")) {
					var c = document.createElement("div");
					c.innerHTML = "<div><span>Upload...</span><div class =\"upload-view-bg\"></div></div>", c.className = "upload-view", E.parentNode.appendChild(c);
					var d = c.getElementsByClassName("upload-view-bg");
					a.upload.onprogress = function(a) {
						d[0].style.width = parseInt(100.1 / a.total * a.loaded) + "%"
					}
				}
				return a
			},
			success: function() {
				var a = document.querySelector(".upload-view");
				a && a.parentNode.removeChild(a), v = !0, d || b("form").submit()
			}
		}) : (v = !0, !d && b("form").submit())
	}

	function m(a) {
		var b = a.originalEvent.changedTouches[0],
			c = document.createEvent("MouseEvent");
		return c.initMouseEvent({
			touchstart: "mousedown",
			touchmove: "mousemove",
			touchend: "mouseup"
		}[a.type], !0, !0, window, 1, b.screenX, b.screenY, b.clientX, b.clientY, !1, !1, !1, !1, 0, null), b.target.dispatchEvent(c), a.preventDefault(), !1
	}

	function n(a, c, d) {
		if(b(".alert-msg").remove(), a) {
			var e = "<div  class=\"alert-msg alert alert-" + (c || "success") + " alert-dismissible\"><i class=\"fa fa-check-circle\"></i>";
			e += a + "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">\xD7</button></div>", "object" == typeof d && null != d.parentNode ? b(d).after(e) : b("#content > .container-fluid,#content > .box").prepend(e)
		}
	}

	function o(a) {
		b.ajax({
			data: {
				settings: a
			},
			type: "post",
			url: "index.php?route=catalog/product/productsImageManagerSettings" + c("token"),
			success: function(c) {
				c = JSON.parse(c), c.error || "modal.html" !== a ? c.success ? (d("content", c.content), p(), n(c.success), b("#im_setting_modal").modal("hide")) : c.error && (n(c.error, "danger"), b("#im_setting_modal").modal("hide")) : (b("#im_setting_modal").remove(), window.preViewCanvas = h, b("body").append(c.content), b("#im_setting_modal").modal("show"))
			}
		})
	}

	function p() {
		if("on" === c("content", "watermark_status")) {
			var a = c("content", "watermark");
			a.image = new Image, a.image.src = a.src, a.image.onerror = function() {
				n(" " + c("lang", "watermark_not_found"), "danger"), a.image = !1
			}
		}
	}

	function q(a, d) {
		if(d && "" != d.text && d.image) {
			a.drawWatermark = function(a, b, c, d) {
				d = parseInt(d) / 100;
				var e = this.width / 100,
					f = Math.min((this.width - 2 * e) / a.width, this.height / a.height) * d;
				if(a.width *= f, a.height *= f, this.context.globalAlpha = parseInt(c) / 100, "center" == b) var g = (this.width - a.width) / 2,
					h = (this.height - a.height) / 2;
				else {
					if("multiple" == b) {
						for(var h, i = parseInt(this.width / a.width), j = parseInt(this.height / a.height), k = 1.5, l = 0; l <= j; l++) {
							h = l * a.height * k;
							for(var g, m = 0; m <= i; m++) g = a.width * m * k + (l % 2 ? a.width / 2 : 0), this.context.drawImage(a, g, h, a.width, a.height)
						}
						return
					}
					var g = /right$/.test(b) ? this.width - a.width - e : e,
						h = /^bottom/.test(b) ? this.height - a.height : 0
				}
				this.context.drawImage(a, g, h, a.width, a.height)
			};
			try {
				d.image.complete ? a.drawWatermark(d.image, d.position, d.opacity, d.size) : (d.image.onload = function() {
					a.drawWatermark(d.image, d.position, d.opacity, d.size)
				}, d.image.onerror = function() {
					return n(" " + c("lang", "watermark_not_found"), "danger", b("#watermark > .row")[0]), !1
				})
			} catch(d) {
				n(" " + c("lang", "watermark_not_found"), "danger", b("#watermark > .row")[0])
			}
		}
	}
	var r = "undefined" == typeof imageManagerTitle;
	b("#image,#input-image").closest("tr,.form-group").remove();
	var s, t = piuJson,
		u = [480, 720, 1080, 2048, 4096, 8400, 10240, "original"],
		v = !0,
		w = [],
		x = b("#file-input"),
		y = b("#drop-files p"),
		a = !1,
		z = c("images_path"),
		A = 0,
		B = {};
	b("#list-view").tooltip({
			selector: ".image i",
			container: "body"
		}), b.event.props.push("dataTransfer"), b("body").bind({
			drop: function() {
				return !1
			},
			dragover: function() {
				return !1
			}
		}), b(function() {
			function c(g) {
				return ds = b(document).scrollTop(), ds >= g - e && 0 < d || ds <= f && 0 > d ? void(d = !1) : void(d && (b(document).scrollTop(ds + d), setTimeout(c, 100)))
			}
			var d = !1,
				e = 0,
				f = 0;
			b("#list-view").sortable({
				scroll: !1,
				handle: ".previewImg",
				start: function(a, c) {
					c.item.addClass("mouseScale"), dh = b(document).height(), f = b("#list-view").offset().top - 50, e = b(window).height()
				},
				sort: function(a) {
					var f = b(document).scrollTop(),
						g = a.pageY - window.pageYOffset;
					d = g >= e - 30 ? 5 : !!(30 >= g) && -5, c(dh)
				},
				deactivate: function(a, c) {
					var e = b("#input-image");
					if(e.length && e.closest(".image").prev().length && (b(".image .img-input")[0].id = "input-image", e[0].id = ""), window.poipInstalled) {
						var f = b(".image")[0];
						f != notPoipModImage && (notPoipModImage.appendChild(f.querySelector(".poip-mod")), notPoipModImage = f)
					}
					setTimeout(function() {
						c.item.removeClass("mouseScale tapScale")
					}, 10), d = !1, B.deactivate && B.deactivate.forEach(function(a) {
						"function" == typeof a && a(b(".image"))
					})
				}
			}), b("#list-view").disableSelection()
		}), b("body").bind("drop dragover dragleave", function(a) {
			(b("#tab-image.active").length || b("[href=\"#tab-image\"].selected").length) && ("dragover" == a.type ? (a.preventDefault(), b(this).attr("class", "dropHover")) : ("drop" == a.type && i(a.dataTransfer.files), b(this).removeAttr("class", "dropHover")))
		}), x.on("change", function() {
			i(b(this)[0].files), x.replaceWith(x = x.val("").clone(!0))
		}),
		function() {
			b(document).on("touchstart touchmove touchend touchcancel", "#list-view .previewImg", function(a) {
				return "touchstart" == a.type ? (stra = 0, ssd = setTimeout(function() {
					b(a.target).parent().addClass("tapScale"), b(document).trigger("touchstart"), m(a), stra = 1
				}, 200)) : ("touchend" == a.type || "touchmove" == a.type) && clearTimeout(ssd), 0 == stra ? void 0 : (m(a), void("touchend" == a.type && b(this).parent().removeClass("tapScale")))
			})
		}(), e();
	var C = [];
	multipleAddImages = function(a) {
		var d = r ? "" : "data/";
		if("object" == typeof a) {
			var e, f = r ? a.parent().find("input") : a.find("input"),
				g = z + d + f.val();
			r ? f.click() : null, -1 == (e = b.inArray(g, C)) ? c("content", "max_images") > C.length && (C.push(g), r ? a.addClass("seBor") : null) : (C.splice(e, 1), r ? a.removeClass("seBor") : null)
		} else if("other_dir" == a)
			for(var h, j = 0; j < C.length; j++) h = r ? b("#modal-image a[href=\"" + C[j] + "\"]") : b("#modal-image #column-right input[value=\"" + C[j].replace(z + d, "") + "\"]"), h.length && (r ? h.addClass("seBor").next().children().prop("checked", !0) : h.parent().addClass("selected"));
		var k = "<button type=\"button\" data-toggle=\"tooltip\" title=\"\" id=\"multi_add\" class=\"btn btn-success\" data-original-title=\"" + c("lang", "mAdd") + "\">";
		k += "<i class=\"fa fa-check\"></i><span>" + C.length + "</span></button>", b("#multi_add").remove(), 0 < C.length && b("#modal-image " + (r ? "#button-delete" : "#refresh")).after(k)
	}, fmHTML = function(a, b) {
		if(a = a.replace(/(\$\(\'#(.*?)delete\'\)\.(bind|on).*?\{)([\s\S]*?\(json(\.|\[')success.+?{)/g, "$1 $('#multi_add').addClass('hidden');$4 $('.piu-fm  #column-right a.selected,.piu-fm input[name^=path]:checked').each(function(e){multipleAddImages($(this));});$('#multi_add').removeClass('hidden');"), !r) {
			var c = "<div class=\"modal-dialog modal-lg im-oc15\"><div  class=\"modal-content\">";
			c += "<div class=\"modal-header\"><button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">\xD7</span></button>", c += "<h4 class=\"modal-title\">" + imageManagerTitle + "</h4></div>", a = a.replace("$('#column-right a').removeAttr('class');", ""), a = a.replace("if ($(this).attr('class') == 'selected')", "multipleAddImages($(this));if ($(this).attr('class') == 'selected')"), a = a.replace("$('#column-right').html(html);", "$('#column-right').html(html);multipleAddImages('other_dir');"), a = a.replace("$(element).offset();", "$(element).position();"), a = a.replace(/\<style[\s\S]*?\/style>/g, ""), a = c + "<div class=\"modal-body\">" + a + "</div></div></div>"
		} else a = a.replace("$('#modal-image').modal('hide');", "");
		return a = b ? a : "<div id=\"modal-image\" class=\"modal piu-fm\">" + a + "</div>", a
	}, b(document).on("ajaxComplete", function(a, c, d) {
		b(".piu-fm:visible").length && void 0 === d.piuFileManager && "GET" === d.type && /filemanager/.test(d.url) && (b(".piu-fm").html(fmHTML(c.responseText, !0)), multipleAddImages("other_dir"))
	}), b("#bySer").on("click", function() {
		var a = b(this);
		a.prop("disabled", !0);
		var d = b("i", this);
		d.attr("class", "fa fa-circle-o-notch fa-spin"), C = [], b(".im-oc15").length ? (b("#modal-image #column-right a.selected").removeClass("selected"), b("#multi_add").remove(), b(".piu-fm").modal("show"), a.prop("disabled", !1), d.attr("class", "fa fa-cloud")) : (b("#modal-image").remove(), b.ajax({
			piuFileManager: !0,
			url: "index.php?route=common/filemanager" + c("token"),
			dataType: "html",
			success: function(c) {
				b("body").append(fmHTML(c)), b("#modal-image").modal("show"), b("#modal-image").on("click", "a,#multi_add", function(a) {
					b(this).is(".thumbnail") && r ? (a.preventDefault(), multipleAddImages(b(this))) : b(this).is("#multi_add") && v && (b("#modal-image").modal("hide"), i(C))
				}), a.prop("disabled", !1), d.attr("class", "fa fa-cloud")
			}
		}))
	}), b("#byUrl").click(function() {
		b("#url-input span").toggle("fast")
	}), b("#url-input button").on("click", function() {
		function a(f) {
			return f-- ? void(d[f] ? b.ajax({
				url: "index.php?route=catalog/product/encodeLinkImage" + c("token"),
				type: "POST",
				contentType: "application/x-www-form-urlencoded",
				data: {
					urls: d[f]
				},
				success: function(c) {
					c = JSON.parse(c), b("#url-input i")[0].style.width = 97 / f + "%", e.push(c), a(f)
				}
			}) : a(f)) : (b("#url-input i").remove(), b("#url-input input").val(""), b("#url-input span").toggle("fast"), void i(e))
		}
		var d = b("#url-input input").val().replace(/ /g, "").split("#"),
			e = [];
		b("#url-input").append("<i></i>");
		var f = d.length;
		setTimeout(function() {
			b("#url-input i")[0] && (b("#url-input i")[0].style.width = 97 / f + "%")
		}, 200), a(f)
	}), b("#list-view").on("click", ".image i.fa-times", function() {
		var a = b(this).closest(".image"),
			d = a.find(".img-input").val();
		v && (b(".tooltip").length && b(".tooltip").remove(), a.is(".old") && "" != d && 1 < c("content", "remove_images") && w.push(d), a.hide("fast", function() {
			this.remove(), e(), B.removeimage && B.removeimage.forEach(function(b) {
				"function" == typeof b && b(a)
			})
		}))
	});
	var D;
	b("#list-view").on("mouseover mouseout", ".image i.fa-times", function(a) {
		null === a.target.parentNode.parentNode.nextElementSibling || ("mouseover" === a.type && 0 > a.target.className.indexOf(" show-del-all") ? D = setTimeout(function() {
			a.target.className += " show-del-all"
		}, 200) : (clearTimeout(D), a.target.className = a.target.className.replace(" show-del-all", "")))
	}), b("#list-view").on("click", ".image .fa-del-all", function() {
		var a = b(this).closest(".image");
		a.nextAll().find("i.fa-times").click(), a.find("i.fa-times").click()
	});
	var E = document.querySelector("a[onclick=\"$('#form').submit();\"],button[type=\"submit\"][form=\"form-product\"]");
	E.parentNode.style = "position: relative;", E.setAttribute("type", ""), E.setAttribute("onclick", ""), E.onclick = function(a) {
		b(".modal form").remove(), a.preventDefault(), v && imagesUpload(!1)
	}, b("#qsave").on("mousedown", function() {
		imagesUpload("before")
	}), b(document).on("ajaxComplete", function(a, b, c) {
		c.url && /qsave/.test(c.url) && imagesUpload(!0)
	}), window.imagesUpload = function(a) {
		return b(".keep_both").click(), b.each(b("#list-view").children(), function(a) {
			var c = b(this).find(".img-input").val(),
				d = "<input class = 'img-input' name='" + (0 == a ? "image" : "product_image[" + a + "][image]") + "' value='" + c + "'  type='hidden' >";
			if(d += 0 == a ? "" : "\n<input name='product_image[" + a + "][sort_order]' value='" + a + "' type='hidden' >", b("input:not([type=checkbox])", this).remove(), b(this).append(d), B.imagesupload && B.imagesupload.forEach(function(a, c) {
					"function" == typeof a && a(b(this), c)
				}), window.poipInstalled)
				for(var e = b(this).find("input[type=checkbox]"), f = 0; f < e.length; f++) e[f].name = e[f].name.replace("index", a)
		}), s || b("#list-view").html("<input name=\"image\" class = \"img-input\" type=\"hidden\" >"), "before" === a || void(0 == A ? (v = !1, l(a)) : (b("button[type=submit]").addClass("shkMe").after("<span  class = 'moment' style='text-align:center;position: absolute;background: #ffffff;border: 1px solid #000;padding: 3px;border-radius: 2px;line-height: 1.2;'>One sec.!</span>"), setTimeout(function() {
			b("button[type=submit]").removeClass("shkMe").next().remove()
		}, 500)))
	}, scaled = "transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd", b(document).on("mousedown touchstart", function(a) {
		var c, d = b(a.target).closest(".image");
		if(b(".z-view").parent().css({
				"z-index": "1",
				transform: ""
			}).one(scaled, function() {
				b(this).css({
					"z-index": ""
				}), b(this).off(scaled)
			}), b(".z-view").remove(), b(".image .fa-search-minus", this).removeClass("fa-search-minus"), b(a.target).is(".fa-search-plus") && "none" == d.css("transform")) {
			b(a.target).addClass("fa-search-minus");
			var e = z + d.find(".img-input").val();
			(c = d.data("upl")) && (e = c.value);
			var f = "",
				g = 2.7 * d.width() > b("#list-view").width() ? b("#list-view").width() / d.width() : 2.7,
				h = d.offset().left - b("#list-view").offset().left,
				i = b("#list-view").height() - (d.offset().top - b("#list-view").offset().top + d.height());
			if(1.5 * (d.width() * g) > b("#list-view").width()) f = "translateX(" + (b("#list-view").width() / 2 - (h + d.width() / 2)) / g + "px)";
			else {
				var j = .1 * ((b("#list-view").width() / 2 - (h + d.width() / 2)) / g),
					k = (d.width() * g - d.width()) / 2,
					l = h + d.width() + k;
				j = j + d.width() * g > b("#list-view").width() ? 0 : j, h < k ? f = "translateX(" + ((k - h) / g + j) + "px)" : b("#list-view").width() < l && (f = "translateX(-" + ((l - b("#list-view").width()) / g - j) + "px)")
			}
			0 > i && (f += " translateY(" + (i - 4) + "px)"), d.css({
				"z-index": "2",
				transform: "scale(" + g + ") " + f
			}), d.one(scaled, function() {
				b(this).prepend("<img class='z-view' src='" + e + "'>"), b(this).off(scaled)
			})
		}
	});
	return b("#piu_settings").click(function() {
		o("modal.html")
	}), b(document).on("click", "#save_setting", function() {
		this.innerHTML = "<i class =\"fa fa-circle-o-notch fa-spin\" ></i>", o(b("#im_setting_modal form").serialize())
	}), p(), {
		on: function(a, b) {
			"string" == typeof a && "function" == typeof b && (B[a] = B[a] ? B[a].push(b) : [b])
		}
	}
}(jQuery);