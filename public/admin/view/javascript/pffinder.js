jQuery.fn.highlight = function(pat) {
	function innerHighlight(node, pat) {
		var skip = 0;
		if (node.nodeType == 3) {
			var pos = node.data.toUpperCase().indexOf(pat);
			pos -= (node.data.substr(0, pos).toUpperCase().length - node.data.substr(0, pos).length);
			if (pos >= 0) {
				var spannode = document.createElement('span');
				spannode.className = 'mp-highlight';
				var middlebit = node.splitText(pos);
				var endbit = middlebit.splitText(pat.length);
				var middleclone = middlebit.cloneNode(true);
				spannode.appendChild(middleclone);
				middlebit.parentNode.replaceChild(spannode, middlebit);
				skip = 1;
			}
		}
		else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
			for (var i = 0; i < node.childNodes.length; ++i) {
				i += innerHighlight(node.childNodes[i], pat);
			}
		}
		return skip;
	}
	return this.length && pat && pat.length ? this.each(function() {
	innerHighlight(this, pat.toUpperCase());
	}) : this;
};

jQuery.fn.removeHighlight = function() {
	return this.find("span.mp-highlight").each(function() {
		this.parentNode.firstChild.nodeName;
		with (this.parentNode) {
			replaceChild(this.firstChild, this);
			normalize();
		}
	}).end();
};
function resethightlight(empty) {
	empty = typeof empty != 'undefined' ? empty : false;
	if(empty == true) { $('.file-finder').val(''); }
	$('.well').removeHighlight();
	$('.checkbox').removeClass('hide');
}
$(document).ready(function() {
	$('.file-finder').on('keyup',function(){ if($(this).val() == '') { resethightlight(); } });	
	$('.file-finder').autocomplete({
		delay: 500,
		source: function(request, response) {
			resethightlight();
			var filter = request;
			filter = filter.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
			$('.well .checkbox').each(function(){					
				if ($(this).text().search(new RegExp(filter, "g")) < 0) { $(this).addClass('hide'); } else { $(this).highlight(filter); }
			});
		}, 
		select: function(event, ui) { return false; },
		focus: function(event, ui) { return false; }
	});
 });