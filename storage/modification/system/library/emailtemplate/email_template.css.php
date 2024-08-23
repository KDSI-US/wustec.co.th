#emailTemplate {
    font-family: <?php echo (!empty($config['body_font_custom']) ? $config['body_font_custom'] . ", " : "") . $config['body_font_family']; ?>;
/* This file is under Git Control by KDSI. */
    font-size: <?php echo $config['body_font_size']; ?>px;
/* This file is under Git Control by KDSI. */
    line-height: 160%;
    color: <?php echo $config['body_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    <?php echo (isset($config['page_shadow']) && $config['page_shadow'] != 'combine-shadow') ? ('box-shadow:' . $config['page_shadow'] . ';') : ''; ?>
/* This file is under Git Control by KDSI. */
}

table {
    border-spacing: 0;
    border-collapse: collapse;
    mso-table-lspace: 0pt;
    mso-table-rspace: 0pt;
}
td {
    font-size: <?php echo $config['body_font_size']; ?>px;
/* This file is under Git Control by KDSI. */
    color: <?php echo $config['body_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    text-align: <?php echo $config['text_align']; ?>;
/* This file is under Git Control by KDSI. */
}
th {
    font-size: <?php echo round($config['body_font_size'] * 0.9); ?>px;
/* This file is under Git Control by KDSI. */
    font-weight: bold;
    color: <?php echo $config['body_heading_color']; ?>;
/* This file is under Git Control by KDSI. */
    text-align: <?php echo $config['text_align']; ?>;
/* This file is under Git Control by KDSI. */
    padding: 10px 5px 8px 5px;
}

/*table {
    <?php /*echo $config['text_align'] == 'right' ? 'direction: rtl;' : ''; */?>
/* This file is under Git Control by KDSI. */
}*/

.table-responsive {
    max-width: 100%;
}


.table-order-default {
    table-layout: auto !important;
}

.table-info > thead > tr > th,
.table-info > tbody > tr > td,
.table-order-default > thead > tr > th,
.table-order-default > tbody > tr > td,
.table-order-default > tfoot > tr > td {
    border: 1px solid #e0e0e0;
}
.table-info > tbody > tr > td {
    padding: 10px 8px;
}
.table-info > tfoot > tr > td {
    padding: 5px 15px;
}
.table-info > thead > tr > th,
.table-order > thead > tr > th {
    background: #ededed;
    color:<?php echo $config['body_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    font-size: 14px;
    line-height: 110%;
    letter-spacing: 0.5px;
    word-spacing: 1px;
    padding: 15px 8px;
}

.product-table td,
.table-order-default td,
.table-info td {
    letter-spacing: 0.2px;
<?php echo ($config['text_align'] == 'center') ? ' text-align:left;' : ''; ?>;
/* This file is under Git Control by KDSI. */
}

.product-table > tbody > tr > td strong, .product-table > tbody > tr > td b,
.table-order-default > tbody > tr > td strong, .table-order-default > tbody > tr > td b,
.table-info > tbody > tr > td strong, .table-info > tbody > tr > td b {
    letter-spacing: 0px;
}

.table-order > tbody > tr > td {
    padding: 8px;
}
.table-order > tfoot > tr > td {
    padding: 10px 8px;
}

.email-product-data {
    font-size: 14px;
    line-height: 18px;
}

img {
    display: block;
    width: auto;
    max-width:100% !important;
    /* height:auto; break Outlook.com online with image disabled */
    line-height: 100%;
    border: none;
    outline: none;
    text-decoration: none;
}

a {
    color:<?php echo $config['body_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    text-decoration:none
}

p,
.email-main-text,
li {
    font-size: <?php echo $config['body_font_size']; ?>px;
/* This file is under Git Control by KDSI. */
    font-weight: normal;
    line-height: 160%;
    color: <?php echo $config['body_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    margin: 0;
    padding: 0;
    word-wrap: break-word;
}

a.link, .email-main-text a {
    color:<?php echo $config['body_link_color']; ?>;
/* This file is under Git Control by KDSI. */
}

.heading1,
.heading2,
.heading3,
.heading4,
.heading5,
.heading6 {
    color:<?php echo $config['body_heading_color']; ?>;
/* This file is under Git Control by KDSI. */
    font-weight:bold;
}
.heading1 {
    color:<?php echo $config['body_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    font-size:<?php echo round($config['body_font_size'] * 1.5); ?>px;
/* This file is under Git Control by KDSI. */
    line-height:22px;
}
.heading2 {
    font-size:<?php echo round($config['body_font_size'] * 1.4); ?>px;
/* This file is under Git Control by KDSI. */
    font-weight:normal;
    line-height:25px;
}
.heading3 {
    font-size:<?php echo round($config['body_font_size'] * 1.3); ?>px;
/* This file is under Git Control by KDSI. */
    font-weight:normal;
    line-height:18px;
}

.table-heading {
<?php if ($config['text_align'] == 'center') { ?>
/* This file is under Git Control by KDSI. */
    float: none;
    margin-left: auto;
    margin-right: auto;
<?php } ?>
/* This file is under Git Control by KDSI. */
}

.link .icon {
    color:<?php echo $config['body_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    line-height: 100%;
    font-size: 120%;
    vertical-align: baseline
}
<?php if ($config['link_style'] == 'button') { ?>
/* This file is under Git Control by KDSI. */
table.link {
    width: 100%;
    margin: 10px 0;
<?php if ($config['text_align'] == 'center') { ?>
/* This file is under Git Control by KDSI. */
    float: none;
    margin-left: auto;
    margin-right: auto;
<?php } ?>
/* This file is under Git Control by KDSI. */
}
table.link td > div {
    border-radius:4px;
    overflow:hidden;
}
.link a, a.link {
    background-color: <?php echo $config['body_link_color']; ?>;
/* This file is under Git Control by KDSI. */
    border-radius: 4px;
    border: 8px solid <?php echo $config['body_link_color']; ?>;
/* This file is under Git Control by KDSI. */
    color: #fff;
    display: inline-block;
    font-size: 12px;
    font-weight: normal;
    line-height: 130%;
    overflow: hidden;
    padding: 0;
    text-align: center;
    text-decoration: none;
}
<?php } else { ?>
/* This file is under Git Control by KDSI. */
table.link {
    <?php if ($config['text_align'] == 'left' || $config['text_align'] == 'right') { ?>float: <?php echo $config['text_align']; ?>;<?php } ?>
/* This file is under Git Control by KDSI. */
}
.link td {
    line-height: 100%;
}
.link a,
a.link {
    color:<?php echo $config['body_link_color']; ?>;
/* This file is under Git Control by KDSI. */
    line-height: 120%;
    font-size: 0.9em;
    word-break: break-all;
}
<?php } ?>
/* This file is under Git Control by KDSI. */

.email-container {
<?php if ($config['page_align'] == 'center'): ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['email_responsive']) : ?>
/* This file is under Git Control by KDSI. */
    width: 100%;
    max-width: <?php echo $config['email_full_width']; ?>;
/* This file is under Git Control by KDSI. */
<?php else: ?>
/* This file is under Git Control by KDSI. */
    width: <?php echo $config['email_full_width']; ?>;
/* This file is under Git Control by KDSI. */
    max-width: 100%;
<?php endif; ?>
/* This file is under Git Control by KDSI. */
    margin-left: auto;
    margin-right: auto;
<?php else: ?>
/* This file is under Git Control by KDSI. */
    width: <?php echo $config['email_full_width']; ?>;
/* This file is under Git Control by KDSI. */
    max-width: 100%;
    float: <?php echo $config['page_align']; ?>;
/* This file is under Git Control by KDSI. */
<?php endif; ?>
/* This file is under Git Control by KDSI. */
}

.email-style {
<?php if($config['body_bg_image']) { ?>
/* This file is under Git Control by KDSI. */
    background-image: url('<?php echo $config['body_bg_image']; ?>');
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php if($config['body_bg_image_repeat']) { ?>
/* This file is under Git Control by KDSI. */
    background-repeat: <?php echo $config['body_bg_image_repeat']; ?>;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php if($config['body_bg_image_position']) { ?>
/* This file is under Git Control by KDSI. */
    background-position: <?php echo $config['body_bg_image_position']; ?>;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
}

.email-main-text {
    padding: <?php
/* This file is under Git Control by KDSI. */
    if ($config['page_padding'] && array_filter($config['page_padding']))
echo $config['page_padding'][0] . 'px ' . $config['page_padding'][1] . 'px ' . $config['page_padding'][2] . 'px ' . $config['page_padding'][3] . 'px';
    else
            echo '10px 17px 5px 17px';
    ?>;
    text-align: <?php echo $config['text_align']; ?>;
/* This file is under Git Control by KDSI. */
}
<?php if (isset($direction) && $direction == 'rtl') { ?>
/* This file is under Git Control by KDSI. */
.email-main-text table {
    margin-right: 0;
    margin-left: auto;
}
<?php } ?>
/* This file is under Git Control by KDSI. */

.email-header-wrap {
<?php if ($config['header_bg_color']) { ?>
/* This file is under Git Control by KDSI. */
    background-color: <?php echo $config['header_bg_color']; ?>;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['header_bg_image']) { ?>
/* This file is under Git Control by KDSI. */
    background-image:url("<?php echo $config['header_bg_image']; ?>");
/* This file is under Git Control by KDSI. */
    background-repeat: repeat-x;
    background-size: auto 100%;
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['border_radius']) { ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['header_border_radius'] && array_filter($config['header_border_radius'])) { ?>
/* This file is under Git Control by KDSI. */
    border-radius: <?php echo $config['header_border_radius'][0]; ?>px <?php echo $config['header_border_radius'][1]; ?>px <?php echo $config['header_border_radius'][2]; ?>px <?php echo $config['header_border_radius'][3]; ?>px;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['header_border_top'] && $config['header_border_top'][0] && $config['header_border_top'][1]) { ?>
    border-top: <?php echo $config['header_border_top'][0]; ?>px solid <?php echo $config['header_border_top'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['header_border_bottom'] && $config['header_border_bottom'][0] && $config['header_border_bottom'][1]) { ?>
    border-bottom: <?php echo $config['header_border_bottom'][0]; ?>px solid <?php echo $config['header_border_bottom'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['header_border_right'] && $config['header_border_right'][0] && $config['header_border_right'][1]) { ?>
    border-right: <?php echo $config['header_border_right'][0]; ?>px solid <?php echo $config['header_border_right'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['header_border_left'] && $config['header_border_left'][0] && $config['header_border_left'][1]) { ?>
    border-left: <?php echo $config['header_border_left'][0]; ?>px solid <?php echo $config['header_border_left'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
} ?>
}

.email-head-text p,
.email-header-html p {
    font-size: inherit;
    font-weight: normal;
    line-height: 100%;
    margin: 0;
    padding: 0;
}
.email-head-text a,
.email-header-html a {
    display: inline-block;
    white-space: nowrap;
    word-spacing: 0;
    font-size: inherit;
    font-weight: normal;
}
.email-header-html a {
    color: #f5f5f5;
}

.email-footer-text {
<?php if ($config['footer_padding'] && array_filter($config['footer_padding'])) {
/* This file is under Git Control by KDSI. */
    echo 'padding: ' . $config['footer_padding'][0] . 'px ' . $config['footer_padding'][1] . 'px ' . $config['footer_padding'][2] . 'px ' . $config['footer_padding'][3] . 'px;';
} ?>
    font-size: <?php echo $config['footer_font_size']; ?>px;
/* This file is under Git Control by KDSI. */
    line-height:normal;
    color:<?php echo $config['footer_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    text-align:center;
    vertical-align:middle
}
.email-footer-text p {
    font-size:<?php echo $config['footer_font_size']; ?>px;
/* This file is under Git Control by KDSI. */
    color:<?php echo $config['footer_font_color']; ?>;
/* This file is under Git Control by KDSI. */
    display: inline-block;
    padding-bottom: 0;
}
.email-footer-text a {
    color:<?php echo $config['footer_font_color']; ?>;
/* This file is under Git Control by KDSI. */
}

.email-footer-wrap {
<?php if ($config['footer_bg_color']) { ?>
/* This file is under Git Control by KDSI. */
    background-color: <?php echo $config['footer_bg_color']; ?>;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['border_radius']) { ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['footer_border_radius'] && array_filter($config['footer_border_radius'])) { ?>
/* This file is under Git Control by KDSI. */
    border-radius: <?php echo $config['footer_border_radius'][0]; ?>px <?php echo $config['footer_border_radius'][1]; ?>px <?php echo $config['footer_border_radius'][2]; ?>px <?php echo $config['footer_border_radius'][3]; ?>px;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['footer_border_top'] && $config['footer_border_top'][0] && $config['footer_border_top'][1]) { ?>
    border-top: <?php echo $config['footer_border_top'][0]; ?>px solid <?php echo $config['footer_border_top'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['footer_border_bottom'] && $config['footer_border_bottom'][0] && $config['footer_border_bottom'][1]) { ?>
    border-bottom: <?php echo $config['footer_border_bottom'][0]; ?>px solid <?php echo $config['footer_border_bottom'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['footer_border_right'] && $config['footer_border_right'][0] && $config['footer_border_right'][1]) { ?>
    border-right: <?php echo $config['footer_border_right'][0]; ?>px solid <?php echo $config['footer_border_right'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['footer_border_left'] && $config['footer_border_left'][0] && $config['footer_border_left'][1]) { ?>
    border-left: <?php echo $config['footer_border_left'][0]; ?>px solid <?php echo $config['footer_border_left'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
}

.email-showcase-wrap {
<?php if ($config['showcase_bg_color']) { ?>
/* This file is under Git Control by KDSI. */
    background-color: <?php echo $config['showcase_bg_color']; ?>;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['border_radius']) { ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['showcase_border_radius'] && array_filter($config['showcase_border_radius'])) { ?>
/* This file is under Git Control by KDSI. */
    border-radius: <?php echo $config['showcase_border_radius'][0]; ?>px <?php echo $config['showcase_border_radius'][1]; ?>px <?php echo $config['showcase_border_radius'][2]; ?>px <?php echo $config['showcase_border_radius'][3]; ?>px;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['showcase_border_top'] && $config['showcase_border_top'][0] && $config['showcase_border_top'][1]) { ?>
    border-top: <?php echo $config[''][0]; ?>px solid <?php echo $config['showcase_border_top'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['showcase_border_bottom'] && $config['showcase_border_bottom'][0] && $config['showcase_border_bottom'][1]) { ?>
    border-bottom: <?php echo $config['showcase_border_bottom'][0]; ?>px solid <?php echo $config['showcase_border_bottom'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['showcase_border_right'] && $config['showcase_border_right'][0] && $config['showcase_border_right'][1]) { ?>
    border-right: <?php echo $config['showcase_border_right'][0]; ?>px solid <?php echo $config['showcase_border_right'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['showcase_border_left'] && $config['showcase_border_left'][0] && $config['showcase_border_left'][1]) { ?>
    border-left: <?php echo $config['showcase_border_left'][0]; ?>px solid <?php echo $config['showcase_border_left'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
}

.email-showcase-inner {
<?php if ($config['showcase_padding'] && array_filter($config['showcase_padding']))
/* This file is under Git Control by KDSI. */
echo 'padding: ' . $config['showcase_padding'][0] . 'px ' . $config['showcase_padding'][1] . 'px ' . $config['showcase_padding'][2] . 'px ' . $config['showcase_padding'][3] . 'px;';
?>
}

.email-showcase-item {   float:left; width:33.33333%; max-width:none;}

.email-showcase-items table {
    font-size: <?php echo $config['body_font_size']; ?>px;
/* This file is under Git Control by KDSI. */
}

.email-page-wrap {
    background-color:<?php echo $config['page_bg_color']; ?>;
/* This file is under Git Control by KDSI. */
<?php if ($config['page_border_radius'] && array_filter($config['page_border_radius'])) { ?>
/* This file is under Git Control by KDSI. */
    border-radius: <?php echo $config['page_border_radius'][0]; ?>px <?php echo $config['page_border_radius'][1]; ?>px <?php echo $config['page_border_radius'][2]; ?>px <?php echo $config['page_border_radius'][3]; ?>px;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php if ($config['border_radius']) {
/* This file is under Git Control by KDSI. */
if ($config['page_border_top'] && $config['page_border_top'][0] && $config['page_border_top'][1]) { ?>
    border-top: <?php echo $config['page_border_top'][0]; ?>px solid <?php echo $config['page_border_top'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['page_border_bottom'] && $config['page_border_bottom'][0] && $config['page_border_bottom'][1]) { ?>
    border-bottom: <?php echo $config['page_border_bottom'][0]; ?>px solid <?php echo $config['page_border_bottom'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['page_border_right'] && $config['page_border_right'][0] && $config['page_border_right'][1]) { ?>
    border-right: <?php echo $config['page_border_right'][0]; ?>px solid <?php echo $config['page_border_right'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php }
/* This file is under Git Control by KDSI. */
if ($config['page_border_left'] && $config['page_border_left'][0] && $config['page_border_left'][1]) { ?>
    border-left: <?php echo $config['page_border_left'][0]; ?>px solid <?php echo $config['page_border_left'][1]; ?>;
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
<?php } ?>
/* This file is under Git Control by KDSI. */
}

<?php if (!empty($config['css_custom'])) echo $config['css_custom']; ?>
/* This file is under Git Control by KDSI. */
