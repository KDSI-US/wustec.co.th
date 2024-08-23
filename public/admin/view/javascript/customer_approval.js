$(document).ready(function (b) {
  b(document).on("click", "#reason_denied-window button.reason_denied-save", function() {
    var f = b(this);
    b.post(b(this).attr("data-url"), b("#popup-form").serialize(), function() {});
    setTimeout(function() {
        var node = this;
        url = '';
        var filter_name = $('input[name=\'filter_name\']').val();
        if (filter_name) {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }
        var filter_email = $('input[name=\'filter_email\']').val();
        if (filter_email) {
            url += '&filter_email=' + encodeURIComponent(filter_email);
        }
        var filter_customer_group_id = $('select[name=\'filter_customer_group_id\']').val();
        if (filter_customer_group_id !== '') {
            url += '&filter_customer_group_id=' + encodeURIComponent(filter_customer_group_id);
        }
        var filter_date_added = $('input[name=\'filter_date_added\']').val();
        if (filter_date_added) {
            url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
        }
        var filter_tax_id = $('input[name=\'filter_tax_id\']').val();
        if (filter_tax_id) {
            url += '&filter_tax_id=' + encodeURIComponent(filter_tax_id);
        }
        var filter_seller_permit = $('input[name=\'filter_seller_permit\']').val();
        if (filter_seller_permit) {
            url += '&filter_seller_permit=' + encodeURIComponent(filter_seller_permit);
        }
        $('#customer-approval').load('index.php?route=customer/customer_approval/customer_approval&user_token={{ user_token }}' + url);
    }, 500)
  });
  b(document).on("click", "#reason_denied", function(i) {
      i.preventDefault();
      var h = b(this);
      var k = b(this).attr("href") + "&popup=true&rand=" + Math.floor(Math.random() * 1100000);
      b("#popup-window").remove();
      var f = b('<div id="popup-window" class="hidden"></div>');
      f.appendTo("body");
      f.append('<div id="reason_denied-window" class="modal" tabindex="-1" role="dialog"><div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content"><div class="modal-body" style="height: 200px;"></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><button type="button" data-url="' + k + '" class="btn btn-primary reason_denied-save" id="save_reason_denied" data-dismiss="modal">Save changes</button></div></div>');
      f.find(".modal-body").load(k, null, function() {
          b("#popup-window").removeClass("hidden");
          b("#reason_denied-window").modal({}).modal("show")
      })
  });
})

var head_customer_approval = document.getElementsByTagName('HEAD')[0];
var link_customer_approval_css = document.createElement('link');
link_customer_approval_css.rel = 'stylesheet';
link_customer_approval_css.type = 'text/css';
link_customer_approval_css.href = 'view/stylesheet/customer_approval.css';
head_customer_approval.appendChild(link_customer_approval_css);