<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="mp-content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" name="savetype" value="savechanges" form="form-orderpro-setting" data-toggle="tooltip" title="<?php echo $button_savechanges; ?>" class="btn btn-primary"><i class="fa fa-refresh"></i> <?php echo $button_savechanges; ?></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><i class="fa fa-life-ring"></i> <?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-wrench"></i> <?php echo $text_edit; ?></h3>
        <div class="pull-right">
	        <div class="storeset pull-left dropdown">
	          <span><?php echo $text_store; ?></span>
	          <button type="button" data-toggle="dropdown" class="btn btn-default btn-xs dropdown-toggle"><span><?php echo $store_name; ?></span> <i class="fa fa-angle-down"></i></button>
	          <ul class="dropdown-menu dropdown-menu-right">
	            <?php foreach($stores as $store) { ?>
	            <?php if(VERSION <= '2.3.0.2') { ?>
	            <li><a href="index.php?route=module_ticket/ticketsetting&amp;token=<?php echo $session_token; ?>&amp;store_id=<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></a></li>
	            <?php } else { ?>
	            <li><a href="index.php?route=module_ticket/ticketsetting&amp;user_token=<?php echo $session_token; ?>&amp;store_id=<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></a></li>
	            <?php } ?>
	            <?php } ?>
	          </ul>
	        </div>
        </div>
      </div>
      <div class="panel-body">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-orderpro-setting" class="form-horizontal">
          <ul class="nav nav-tabs" style="position: relative;">
            <li class="active"><a href="#tab-general" data-toggle="tab"><i class="fa fa-wrench"></i> <span><?php echo $tab_general; ?></span></a></li>
            <li><a href="#tab-page" data-toggle="tab"><i class="fa fa-file-o"></i> <span><?php echo $tab_page; ?></span></a></li>
            <li><a href="#tab-language-setting" data-toggle="tab"><i class="fa fa-language"></i> <span><?php echo $tab_language_setting; ?></span></a></li>
            <li><a href="#tab-emailnotification" data-toggle="tab"><i class="fa fa-bell"></i> <span><?php echo $tab_emailnotification; ?></span></a></li>
            <li class="headerfooter <?php echo !$ticketsetting_headerfooter ? 'hide' : ''; ?>"><a href="#tab-headerfooter" data-toggle="tab"><i class="fa fa-tag"></i> <span><?php echo $tab_headerfooter; ?></span></a></li>
            <li><a href="#tab-support" data-toggle="tab"><i class="fa fa-thumbs-up"></i> <span><?php echo $tab_support; ?></span></a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group mp-buttons">
                <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
                <div class="col-md-4 col-sm-5">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_status) ? 'active' : ''; ?>">
                      <input type="radio" name="ticketsetting_status" value="1" <?php echo (!empty($ticketsetting_status)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_enabled; ?>
                    </label>
                    <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_status) ? 'active' : ''; ?>">
                      <input type="radio" name="ticketsetting_status" value="0" <?php echo (empty($ticketsetting_status)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_disabled; ?>
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group mp-buttons hide">
                <label class="col-sm-2 control-label"><?php echo $entry_headerfooter; ?></label>
                <div class="col-md-4 col-sm-5">
                  <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="header-status btn btn-primary <?php echo !empty($ticketsetting_headerfooter) ? 'active' : ''; ?>">
                      <input type="radio" name="ticketsetting_headerfooter" value="1" <?php echo (!empty($ticketsetting_headerfooter)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_module_headerfooter; ?>
                    </label>
                    <label class="header-status btn btn-primary <?php echo empty($ticketsetting_headerfooter) ? 'active' : ''; ?>">
                      <input type="radio" name="ticketsetting_headerfooter" value="0" <?php echo (empty($ticketsetting_headerfooter)) ? 'checked="checked"' : ''; ?> />
                      <?php echo $text_theme_headerfooter; ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-page">
            	<div class="row">
                <div class="col-sm-3">
                  <ul class="nav nav-pills nav-stacked ostab" id="panels">
                    <li><a class="text-left" href="#navtab-landing-page" data-toggle="tab"><i class="fa fa-globe" aria-hidden="true"></i> <?php echo $navtab_landing_page; ?></a></li>
                    <li><a class="text-left" href="#navtab-ticketsubmit-page" data-toggle="tab"><i class="fa fa-life-ring" aria-hidden="true"></i> <?php echo $navtab_ticketsubmit_page; ?></a></li>
                    <li><a class="text-left" href="#navtab-ticketlist-page" data-toggle="tab"><i class="fa fa-list-ol" aria-hidden="true"></i> <?php echo $navtab_ticketlist_page; ?></a></li>
                    <li><a class="text-left" href="#navtab-ticketview-page" data-toggle="tab"><i class="fa fa-street-view" aria-hidden="true"></i> <?php echo $navtab_ticketview_page; ?></a></li>
                  </ul>
                </div>
                <div class="col-sm-9">
                	<div class="tab-content">
                		<div class="tab-pane" id="navtab-landing-page">
				              <fieldset>
				                <legend><i class="fa fa-globe"></i> <?php echo $legend_banner; ?></legend>
				                <div class="form-group">
				                  <label class="col-sm-2 control-label" for="input-banner"><?php echo $entry_banner; ?></label>
				                  <div class="col-sm-10"><a href="" id="thumb-banner" data-toggle="image" class="img-thumbnail"><img src="<?php echo $banner; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
				                    <input type="hidden" name="ticketsetting_banner" value="<?php echo $ticketsetting_banner; ?>" id="input-banner" />
				                  </div>
				                </div>
				              </fieldset>
				              <fieldset>
				                <legend><?php echo $legend_widgets; ?></legend>
				                <div class="form-group mp-buttons">
				                  <label class="col-sm-2 control-label"><?php echo $entry_ticketsubmission; ?></label>
				                  <div class="col-md-4 col-sm-5">
				                    <div class="btn-group btn-group-justified" data-toggle="buttons">
				                      <label class="btn btn-primary <?php echo !empty($ticketsetting_widgets['ticketsubmission']) ? 'active' : ''; ?>">
				                        <input type="radio" name="ticketsetting_widgets[ticketsubmission]" value="1" <?php echo (!empty($ticketsetting_widgets['ticketsubmission'])) ? 'checked="checked"' : ''; ?> />
				                        <?php echo $text_enabled; ?>
				                      </label>
				                      <label class="btn btn-primary <?php echo empty($ticketsetting_widgets['ticketsubmission']) ? 'active' : ''; ?>">
				                        <input type="radio" name="ticketsetting_widgets[ticketsubmission]" value="0" <?php echo (empty($ticketsetting_widgets['ticketsubmission'])) ? 'checked="checked"' : ''; ?> />
				                        <?php echo $text_disabled; ?>
				                      </label>
				                    </div>
				                  </div>
				                </div>
				                <div class="form-group mp-buttons">
				                  <label class="col-sm-2 control-label"><?php echo $entry_videos; ?></label>
				                  <div class="col-md-4 col-sm-5">
				                    <div class="btn-group btn-group-justified" data-toggle="buttons">
				                      <label class="btn btn-primary <?php echo !empty($ticketsetting_widgets['videos']) ? 'active' : ''; ?>">
				                        <input type="radio" name="ticketsetting_widgets[videos]" value="1" <?php echo (!empty($ticketsetting_widgets['videos'])) ? 'checked="checked"' : ''; ?> />
				                        <?php echo $text_enabled; ?>
				                      </label>
				                      <label class="btn btn-primary <?php echo empty($ticketsetting_widgets['videos']) ? 'active' : ''; ?>">
				                        <input type="radio" name="ticketsetting_widgets[videos]" value="0" <?php echo (empty($ticketsetting_widgets['videos'])) ? 'checked="checked"' : ''; ?> />
				                        <?php echo $text_disabled; ?>
				                      </label>
				                    </div>
				                  </div>
				                </div>
				                <div class="form-group mp-buttons">
				                  <label class="col-sm-2 control-label"><?php echo $entry_knowledgebase; ?></label>
				                  <div class="col-md-4 col-sm-5">
				                    <div class="btn-group btn-group-justified" data-toggle="buttons">
				                      <label class="btn btn-primary <?php echo !empty($ticketsetting_widgets['knowledgebase']) ? 'active' : ''; ?>">
				                        <input type="radio" name="ticketsetting_widgets[knowledgebase]" value="1" <?php echo (!empty($ticketsetting_widgets['knowledgebase'])) ? 'checked="checked"' : ''; ?> />
				                        <?php echo $text_enabled; ?>
				                      </label>
				                      <label class="btn btn-primary <?php echo empty($ticketsetting_widgets['knowledgebase']) ? 'active' : ''; ?>">
				                        <input type="radio" name="ticketsetting_widgets[knowledgebase]" value="0" <?php echo (empty($ticketsetting_widgets['knowledgebase'])) ? 'checked="checked"' : ''; ?> />
				                        <?php echo $text_disabled; ?>
				                      </label>
				                    </div>
				                  </div>
				                </div>
				              </fieldset>
				            </div>
				            <div class="tab-pane" id="navtab-ticketsubmit-page">
				            	<fieldset>
				            		<legend><i class="fa fa-life-ring"></i> <?php echo $legend_ticketsubmit; ?></legend>
					            	<div class="form-group mp-buttons">
			                  			<label class="col-sm-2 control-label"><?php echo $entry_captcha; ?></label>
					                  	<div class="col-md-4 col-sm-5">
						                    <div class="btn-group btn-group-justified" data-toggle="buttons">
						                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_captcha) ? 'active' : ''; ?>">
						                        <input type="radio" name="ticketsetting_captcha" value="1" <?php echo (!empty($ticketsetting_captcha)) ? 'checked="checked"' : ''; ?> />
						                        <?php echo $text_enabled; ?>
						                      </label>
						                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_captcha) ? 'active' : ''; ?>">
						                        <input type="radio" name="ticketsetting_captcha" value="0" <?php echo (empty($ticketsetting_captcha)) ? 'checked="checked"' : ''; ?> />
						                        <?php echo $text_disabled; ?>
						                      </label>
						                    </div>
					                  	</div>
				                	</div>
					                <div class="form-group required">
				                  		<label class="col-sm-2 control-label" for="input-ticketstatus"><span data-toggle="tooltip" title="<?php echo $help_ticketstatus; ?>"><?php echo $entry_ticketstatus; ?></span></label>
					                  	<div class="col-md-4 col-sm-5">
						                    <select name="ticketsetting_ticketstatus_id" id="input-ticketstatus" class="form-control">
						                      <?php foreach ($ticketstatuses as $ticketstatus) { ?>
						                      <?php if ($ticketstatus['ticketstatus_id'] == $ticketsetting_ticketstatus_id) { ?>
						                      <option value="<?php echo $ticketstatus['ticketstatus_id']; ?>" selected="selected"><?php echo $ticketstatus['name']; ?></option>
						                      <?php } else { ?>
						                      <option value="<?php echo $ticketstatus['ticketstatus_id']; ?>"><?php echo $ticketstatus['name']; ?></option>
						                      <?php } ?>
						                      <?php } ?>
						                    </select>
						                    <?php if($error_ticketstatus_id) { ?>
						                    <div class="text-danger"><?php echo $error_ticketstatus_id; ?></div>
						                    <?php } ?>
					                  	</div>
					                </div>
					                <div class="form-group required">
					                  	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_closed_ticketstatus; ?>"><?php echo $entry_closed_ticketstatus; ?></span></label>
					                  	<div class="col-md-4 col-sm-5">
						                    <select name="ticketsetting_ticketstatus_closed_id" class="form-control">
						                      <?php foreach ($ticketstatuses as $ticketstatus) { ?>
						                      <?php if ($ticketstatus['ticketstatus_id'] == $ticketsetting_ticketstatus_closed_id) { ?>
						                      <option value="<?php echo $ticketstatus['ticketstatus_id']; ?>" selected="selected"><?php echo $ticketstatus['name']; ?></option>
						                      <?php } else { ?>
						                      <option value="<?php echo $ticketstatus['ticketstatus_id']; ?>"><?php echo $ticketstatus['name']; ?></option>
						                      <?php } ?>
						                      <?php } ?>
						                    </select>
						                    <?php if($error_ticketstatus_id) { ?>
						                    <div class="text-danger"><?php echo $error_ticketstatus_id; ?></div>
						                    <?php } ?>
					                  	</div>
					                </div>
				                </fieldset>
			                </div>
				            <div class="tab-pane" id="navtab-ticketlist-page">
				            	<fieldset>
				            		<legend><i class="fa fa-list-ol"></i> <?php echo $legend_ticketlistpage; ?></legend>

				            	<div class="form-group required">
			                  <label class="col-sm-2 control-label"><?php echo $entry_list_limit; ?></label>
			                  <div class="col-md-3">
			                  	<input type="text" name="ticketsetting_list_limit" value="<?php echo $ticketsetting_list_limit; ?>" class="form-control" placeholder="<?php echo $entry_list_limit; ?>" />
			                  	<?php if($error_list_limit) { ?>
				                    <div class="text-danger"><?php echo $error_list_limit; ?></div>
				                    <?php } ?>
			                  </div>
			                </div>
			                </fieldset>
				            </div>
				            <div class="tab-pane" id="navtab-ticketview-page">
				            	<fieldset>
				                	<legend><i class="fa fa-street-view"></i> <?php echo $legend_ticketreply; ?></legend>
				                	<div class="form-group mp-buttons">
			                  		<label class="col-sm-2 control-label"><?php echo $entry_userphoto_display; ?></label>
				                  	<div class="col-md-4 col-sm-5">
					                    <div class="btn-group btn-group-justified" data-toggle="buttons">
					                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_userphoto_display) ? 'active' : ''; ?>">
					                        <input type="radio" name="ticketsetting_userphoto_display" value="1" <?php echo (!empty($ticketsetting_userphoto_display)) ? 'checked="checked"' : ''; ?> />
					                        <?php echo $text_show; ?>
					                      </label>
					                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_userphoto_display) ? 'active' : ''; ?>">
					                        <input type="radio" name="ticketsetting_userphoto_display" value="0" <?php echo (empty($ticketsetting_userphoto_display)) ? 'checked="checked"' : ''; ?> />
					                        <?php echo $text_hide; ?>
					                      </label>
					                    </div>
				                  	</div>
				                	</div>
				                	<div class="form-group mp-buttons">
			                  		<label class="col-sm-2 control-label"><?php echo $entry_staffphoto_display; ?></label>
				                  	<div class="col-md-4 col-sm-5">
					                    <div class="btn-group btn-group-justified" data-toggle="buttons">
					                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_staffphoto_display) ? 'active' : ''; ?>">
					                        <input type="radio" name="ticketsetting_staffphoto_display" value="1" <?php echo (!empty($ticketsetting_staffphoto_display)) ? 'checked="checked"' : ''; ?> />
					                        <?php echo $text_show; ?>
					                      </label>
					                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_staffphoto_display) ? 'active' : ''; ?>">
					                        <input type="radio" name="ticketsetting_staffphoto_display" value="0" <?php echo (empty($ticketsetting_staffphoto_display)) ? 'checked="checked"' : ''; ?> />
					                        <?php echo $text_hide; ?>
					                      </label>
					                    </div>
				                  	</div>
				                	</div>
				                	<div class="form-group required">
					                  <label class="col-sm-2 control-label"><?php echo $entry_reply_list_limit; ?></label>
					                  <div class="col-md-3">
					                  	<input type="text" name="ticketsetting_reply_list_limit" value="<?php echo $ticketsetting_reply_list_limit; ?>" class="form-control" placeholder="<?php echo $entry_reply_list_limit; ?>" />
					                  	<?php if($error_reply_list_limit) { ?>
					                    <div class="text-danger"><?php echo $error_reply_list_limit; ?></div>
					                    <?php } ?>
					                  </div>
					                </div>
				                </fieldset>
				                <br/><br/>
				                <fieldset>
				                	<legend><?php echo $legend_admin_panel; ?></legend>
				                	<div class="form-group mp-buttons">
			                  		<label class="col-sm-2 control-label"><?php echo $entry_adminreply_close_status; ?></label>
				                  	<div class="col-md-4 col-sm-5">
					                    <div class="btn-group btn-group-justified" data-toggle="buttons">
					                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_adminreply_close_status) ? 'active' : ''; ?>">
					                        <input type="radio" name="ticketsetting_adminreply_close_status" value="1" <?php echo (!empty($ticketsetting_adminreply_close_status)) ? 'checked="checked"' : ''; ?> />
					                        <?php echo $text_yes; ?>
					                      </label>
					                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_adminreply_close_status) ? 'active' : ''; ?>">
					                        <input type="radio" name="ticketsetting_adminreply_close_status" value="0" <?php echo (empty($ticketsetting_adminreply_close_status)) ? 'checked="checked"' : ''; ?> />
					                        <?php echo $text_no; ?>
					                      </label>
					                    </div>
				                  	</div>
				                	</div>
				                </fieldset>
				            </div>
			            </div>
		            </div>
	            </div>
            </div>
            <div class="tab-pane" id="tab-language-setting">
            	<div class="row">
                <div class="col-sm-3">
                  <ul class="nav nav-pills nav-stacked ostab" id="lang-panels">
                    <li><a class="text-left" href="#navtab-lang-landing-page" data-toggle="tab"><i class="fa fa-globe" aria-hidden="true"></i> <?php echo $navtab_landing_page; ?></a></li>
                    <li><a class="text-left" href="#navtab-lang-ticketsubmit-page" data-toggle="tab"><i class="fa fa-life-ring" aria-hidden="true"></i> <?php echo $navtab_ticketsubmit_page; ?></a></li>
                    <li><a class="text-left" href="#navtab-lang-ticketsuccess-page" data-toggle="tab"><i class="fa fa-thumbs-up" aria-hidden="true"></i> <?php echo $navtab_ticketsuccess_page; ?></a></li>
                  </ul>
                </div>
                <div class="col-sm-9">
                  <div class="tab-content">
                    <div class="tab-pane" id="navtab-lang-landing-page">
											<ul class="nav nav-tabs" id="language">
			                	<?php foreach ($languages as $language) { ?>
				                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php if(VERSION >= '2.2.0.0') { ?>
                            <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" />
                            <?php } else{ ?>
                            <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                            <?php } ?> <?php echo $language['name']; ?></a></li>
				                <?php } ?>
			              	</ul>
			              	<div class="tab-content">
				                <?php foreach ($languages as $language) { ?>
				                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
				                  	<div class="form-group required">
				                    	<label class="col-sm-2 control-label" for="input-title<?php echo $language['language_id']; ?>"><?php echo $entry_banner_title; ?></label>
				                    	<div class="col-sm-10">
				                      		<input type="text" name="ticketsetting_language[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($ticketsetting_language[$language['language_id']]) ? $ticketsetting_language[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_banner_title; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
			                      		 	<?php if(isset($error_title[$language['language_id']])) { ?>
							                    <div class="text-danger"><?php echo $error_title[$language['language_id']]; ?></div>
							                    <?php } ?>
				                    	</div>
				                  	</div>
				                  	<div class="form-group required">
					                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
					                    <div class="col-sm-10">
					                      <input type="text" name="ticketsetting_language[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($ticketsetting_language[$language['language_id']]) ? $ticketsetting_language[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
					                      <?php if(isset($error_meta_title[$language['language_id']])) { ?>
						                    <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
						                    <?php } ?>
					                    </div>
					                  </div>
					                  <div class="form-group">
					                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
					                    <div class="col-sm-10">
					                      <textarea name="ticketsetting_language[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($ticketsetting_language[$language['language_id']]) ? $ticketsetting_language[$language['language_id']]['meta_description'] : ''; ?></textarea>
					                    </div>
					                  </div>
					                  <div class="form-group">
					                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
					                    <div class="col-sm-10">
					                      <textarea name="ticketsetting_language[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($ticketsetting_language[$language['language_id']]) ? $ticketsetting_language[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
					                    </div>
					                  </div>
				              	</div>
				              	<?php } ?>
				          		</div>
			          		</div>
			          		<div class="tab-pane" id="navtab-lang-ticketsubmit-page">
											<ul class="nav nav-tabs" id="ticketsubmit-language">
			                	<?php foreach ($languages as $language) { ?>
				                <li><a href="#ticketsubmit-language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php if(VERSION >= '2.2.0.0') { ?>
                            <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" />
                            <?php } else{ ?>
                            <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                            <?php } ?> <?php echo $language['name']; ?></a></li>
				                <?php } ?>
			              	</ul>
			              	<div class="tab-content">
				                <?php foreach ($languages as $language) { ?>
				                <div class="tab-pane" id="ticketsubmit-language<?php echo $language['language_id']; ?>">
				                  	<div class="form-group required">
					                    <label class="col-sm-2 control-label" for="input-banner-title<?php echo $language['language_id']; ?>"><?php echo $entry_banner_title; ?></label>
					                    <div class="col-sm-10">
					                      <input type="text" name="ticketsetting_submit_language[<?php echo $language['language_id']; ?>][banner_title]" value="<?php echo isset($ticketsetting_submit_language[$language['language_id']]) ? $ticketsetting_submit_language[$language['language_id']]['banner_title'] : ''; ?>" placeholder="<?php echo $entry_banner_title; ?>" id="input-banner-title<?php echo $language['language_id']; ?>" class="form-control" />
					                      <?php if(isset($error_submit_banner_title[$language['language_id']])) { ?>
						                    <div class="text-danger"><?php echo $error_submit_banner_title[$language['language_id']]; ?></div>
						                    <?php } ?>
					                    </div>
					                  </div>
					                  <div class="form-group required">
				                    	<label class="col-sm-2 control-label" for="input-title<?php echo $language['language_id']; ?>"><?php echo $entry_title; ?></label>
				                    	<div class="col-sm-10">
				                      		<input type="text" name="ticketsetting_submit_language[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($ticketsetting_submit_language[$language['language_id']]) ? $ticketsetting_submit_language[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
				                      		<?php if(isset($error_submit_title[$language['language_id']])) { ?>
							                    <div class="text-danger"><?php echo $error_submit_title[$language['language_id']]; ?></div>
							                    <?php } ?>
				                    	</div>
				                  	</div>
				                  	<div class="form-group required">
					                    <label class="col-sm-2 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
					                    <div class="col-sm-10">
					                      <input type="text" name="ticketsetting_submit_language[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($ticketsetting_submit_language[$language['language_id']]) ? $ticketsetting_submit_language[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title<?php echo $language['language_id']; ?>" class="form-control" />
					                      <?php if(isset($error_meta_title[$language['language_id']])) { ?>
						                    <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
						                    <?php } ?>
					                    </div>
					                  </div>
					                  <div class="form-group">
					                    <label class="col-sm-2 control-label" for="input-meta-description<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
					                    <div class="col-sm-10">
					                      <textarea name="ticketsetting_submit_language[<?php echo $language['language_id']; ?>][meta_description]" rows="5" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($ticketsetting_submit_language[$language['language_id']]) ? $ticketsetting_submit_language[$language['language_id']]['meta_description'] : ''; ?></textarea>
					                    </div>
					                  </div>
					                  <div class="form-group">
					                    <label class="col-sm-2 control-label" for="input-meta-keyword<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
					                    <div class="col-sm-10">
					                      <textarea name="ticketsetting_submit_language[<?php echo $language['language_id']; ?>][meta_keyword]" rows="5" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($ticketsetting_submit_language[$language['language_id']]) ? $ticketsetting_submit_language[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
					                    </div>
					                  </div>
				              	</div>
				              	<?php } ?>
				          		</div>
			          		</div>
			          		<div class="tab-pane" id="navtab-lang-ticketsuccess-page">
											<ul class="nav nav-tabs" id="ticketsuccess-language">
			                	<?php foreach ($languages as $language) { ?>
				                <li><a href="#ticketsuccess-language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php if(VERSION >= '2.2.0.0') { ?>
                            <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" />
                            <?php } else{ ?>
                            <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                            <?php } ?> <?php echo $language['name']; ?></a></li>
				                <?php } ?>
			              	</ul>
			              	<div class="tab-content">
				                <?php foreach ($languages as $language) { ?>
				                <div class="tab-pane" id="ticketsuccess-language<?php echo $language['language_id']; ?>">
			                  	<div class="form-group required">
				                    <label class="col-sm-2 control-label" for="input-banner-title<?php echo $language['language_id']; ?>"><?php echo $entry_banner_title; ?></label>
				                    <div class="col-sm-10">
				                      <input type="text" name="ticketsetting_success_language[<?php echo $language['language_id']; ?>][banner_title]" value="<?php echo isset($ticketsetting_success_language[$language['language_id']]) ? $ticketsetting_success_language[$language['language_id']]['banner_title'] : ''; ?>" placeholder="<?php echo $entry_banner_title; ?>" id="input-banner-title<?php echo $language['language_id']; ?>" class="form-control" />
				                      <?php if(isset($error_success_banner_title[$language['language_id']])) { ?>
					                    <div class="text-danger"><?php echo $error_success_banner_title[$language['language_id']]; ?></div>
					                    <?php } ?>
				                    </div>
				                  </div>
				                  <div class="form-group required">
			                    	<label class="col-sm-2 control-label" for="input-title<?php echo $language['language_id']; ?>"><?php echo $entry_title; ?></label>
			                    	<div class="col-sm-10">
			                      		<input type="text" name="ticketsetting_success_language[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($ticketsetting_success_language[$language['language_id']]) ? $ticketsetting_success_language[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
			                      		<?php if(isset($error_success_title[$language['language_id']])) { ?>
						                    <div class="text-danger"><?php echo $error_success_title[$language['language_id']]; ?></div>
						                    <?php } ?>
			                    	</div>
			                  	</div>
				                  <div class="form-group">
				                    <label class="col-sm-2 control-label" for="input-success-notlogged-description<?php echo $language['language_id']; ?>"><?php echo $entry_success_notlogged_description; ?></label>
				                    <div class="col-sm-10">
				                      <textarea name="ticketsetting_success_language[<?php echo $language['language_id']; ?>][notlogged_description]" rows="5" placeholder="<?php echo $entry_success_notlogged_description; ?>" id="input-success-notlogged-description<?php echo $language['language_id']; ?>" class="form-control summernote" data-toggle="summernote" data-lang="<?php echo $summernote; ?>"><?php echo isset($ticketsetting_success_language[$language['language_id']]) ? $ticketsetting_success_language[$language['language_id']]['notlogged_description'] : ''; ?></textarea>
				                    </div>
				                  </div>
				                  <div class="form-group">
				                    <label class="col-sm-2 control-label" for="input-success-logged-description<?php echo $language['language_id']; ?>"><?php echo $entry_success_logged_description; ?></label>
				                    <div class="col-sm-10">
				                      <textarea name="ticketsetting_success_language[<?php echo $language['language_id']; ?>][logged_description]" rows="5" placeholder="<?php echo $entry_success_logged_description; ?>" id="input-success-logged-description<?php echo $language['language_id']; ?>" class="form-control summernote" data-toggle="summernote" data-lang="<?php echo $summernote; ?>"><?php echo isset($ticketsetting_success_language[$language['language_id']]) ? $ticketsetting_success_language[$language['language_id']]['logged_description'] : ''; ?></textarea>
				                    </div>
				                  </div>
				              	</div>
				              	<?php } ?>
				          		</div>
			          		</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-headerfooter">
              <fieldset>
                <legend><?php echo $legend_header; ?></legend>
                <div class="form-group">
                  <label class="col-sm-2 control-label" for="input-logo"><?php echo $entry_logo; ?></label>
                  <div class="col-sm-10"><a href="" id="thumb-logo" data-toggle="image" class="img-thumbnail"><img src="<?php echo $logo; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
                    <input type="hidden" name="ticketsetting_logo" value="<?php echo $ticketsetting_logo; ?>" id="input-logo" />
                  </div>
                </div>
                <div class="form-group mp-buttons">
                  <label class="col-sm-2 control-label"><?php echo $entry_ticket_submission_link; ?></label>
                  <div class="col-md-4 col-sm-5">
                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_ticket_submission_link) ? 'active' : ''; ?>">
                        <input type="radio" name="ticketsetting_ticket_submission_link" value="1" <?php echo (!empty($ticketsetting_ticket_submission_link)) ? 'checked="checked"' : ''; ?> />
                        <?php echo $text_show; ?>
                      </label>
                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_ticket_submission_link) ? 'active' : ''; ?>">
                        <input type="radio" name="ticketsetting_ticket_submission_link" value="0" <?php echo (empty($ticketsetting_ticket_submission_link)) ? 'checked="checked"' : ''; ?> />
                        <?php echo $text_hide; ?>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group mp-buttons">
                  <label class="col-sm-2 control-label"><?php echo $entry_login_link; ?></label>
                  <div class="col-md-4 col-sm-5">
                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_login_link) ? 'active' : ''; ?>">
                        <input type="radio" name="ticketsetting_login_link" value="1" <?php echo (!empty($ticketsetting_login_link)) ? 'checked="checked"' : ''; ?> />
                        <?php echo $text_show; ?>
                      </label>
                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_login_link) ? 'active' : ''; ?>">
                        <input type="radio" name="ticketsetting_login_link" value="0" <?php echo (empty($ticketsetting_login_link)) ? 'checked="checked"' : ''; ?> />
                        <?php echo $text_hide; ?>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 control-label"><?php echo $entry_logo_link; ?></label>
                  <div class="col-md-10">
                  	<input type="text" name="ticketsetting_logo_link" value="<?php echo $ticketsetting_logo_link; ?>" class="form-control" placeholder="<?php echo $entry_logo_link; ?>" />
                  </div>
                </div>
                <div class="form-group mp-buttons">
              		<label class="col-sm-2 control-label"><?php echo $entry_userphoto_display; ?></label>
                	<div class="col-md-4 col-sm-5">
                    <div class="btn-group btn-group-justified" data-toggle="buttons">
                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_userphoto_display_header) ? 'active' : ''; ?>">
                        <input type="radio" name="ticketsetting_userphoto_display_header" value="1" <?php echo (!empty($ticketsetting_userphoto_display_header)) ? 'checked="checked"' : ''; ?> />
                        <?php echo $text_show; ?>
                      </label>
                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_userphoto_display_header) ? 'active' : ''; ?>">
                        <input type="radio" name="ticketsetting_userphoto_display_header" value="0" <?php echo (empty($ticketsetting_userphoto_display_header)) ? 'checked="checked"' : ''; ?> />
                        <?php echo $text_hide; ?>
                      </label>
                    </div>
                	</div>
              	</div>
              </fieldset>
              <fieldset>
                <legend><?php echo $legend_footer; ?></legend>
                <ul class="nav nav-tabs" id="footer-language">
                  <?php foreach ($languages as $language) { ?>
                  <li><a href="#footer-language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php if(VERSION >= '2.2.0.0') { ?>
                            <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" />
                            <?php } else{ ?>
                            <img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
                            <?php } ?> <?php echo $language['name']; ?></a></li>
                  <?php } ?>
                </ul>
                <div class="tab-content">
                  <?php foreach ($languages as $language) { ?>
                  <div class="tab-pane" id="footer-language<?php echo $language['language_id']; ?>">
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="input-footer<?php echo $language['language_id']; ?>"><?php echo $entry_footer; ?></label>
                      <div class="col-sm-10">
                        <textarea name="ticketsetting_language[<?php echo $language['language_id']; ?>][footer]" placeholder="<?php echo $entry_footer; ?>" id="input-footer<?php echo $language['language_id']; ?>" class="form-control summernote" data-toggle="summernote" data-lang="<?php echo $summernote; ?>"><?php echo isset($ticketsetting_language[$language['language_id']]['footer']) ? $ticketsetting_language[$language['language_id']]['footer'] : ''; ?></textarea>
                      </div>
                    </div>
                  </div>
                  <?php } ?>
                </div>
              </fieldset>
            </div>
            <div class="tab-pane" id="tab-emailnotification">
            	<fieldset>
	            	<div class="form-group required">
	                <label class="col-sm-2 control-label" for="input-adminemail"><?php echo $entry_adminemail; ?></label>
	                <div class="col-sm-5">
	                  <input type="text" name="ticketsetting_adminemail" placeholder="<?php echo $entry_adminemail; ?>" id="input-adminemail" class="form-control" value="<?php echo $ticketsetting_adminemail; ?>" />
	                </div>
	              </div>
              </fieldset>
              <br/><br/>
            	<div class="row">
                <div class="col-sm-3">
                  <ul class="nav nav-pills nav-stacked ostab" id="email-panels">
                    <li><a class="text-left" href="#tab-email_user" data-toggle="tab"><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo $tab_email_user; ?></a></li>
                    <li><a class="text-left" href="#tab-email_admin" data-toggle="tab"><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo $tab_email_admin; ?></a></li>
                  </ul>
                </div>
                <div class="col-sm-9">
                	<div class="tab-content">
                		<div class="tab-pane" id="tab-email_user">
              			 	<div class="form-group mp-buttons">
		              			<label class="col-sm-2 control-label"><?php echo $emailtab_createtickettouser; ?></label>
			                	<div class="col-md-4 col-sm-5">
			                    <div class="btn-group btn-group-justified" data-toggle="buttons">
			                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_email['createtickettouser']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[createtickettouser][status]" value="1" <?php echo (!empty($ticketsetting_email['createtickettouser']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_enabled; ?>
			                      </label>
			                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_email['createtickettouser']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[createtickettouser][status]" value="0" <?php echo (empty($ticketsetting_email['createtickettouser']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_disabled; ?>
			                      </label>
			                    </div>
			                	</div>
		              		</div>
		              		<div class="form-group mp-buttons">
		              			<label class="col-sm-2 control-label"><?php echo $emailtab_adminreplytouser; ?></label>
			                	<div class="col-md-4 col-sm-5">
			                    <div class="btn-group btn-group-justified" data-toggle="buttons">
			                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_email['adminreplytouser']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[adminreplytouser][status]" value="1" <?php echo (!empty($ticketsetting_email['adminreplytouser']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_enabled; ?>
			                      </label>
			                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_email['adminreplytouser']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[adminreplytouser][status]" value="0" <?php echo (empty($ticketsetting_email['adminreplytouser']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_disabled; ?>
			                      </label>
			                    </div>
			                	</div>
		              		</div>
		              		<div class="form-group mp-buttons">
		              			<label class="col-sm-2 control-label"><?php echo $emailtab_createusertouser; ?></label>
			                	<div class="col-md-4 col-sm-5">
			                    <div class="btn-group btn-group-justified" data-toggle="buttons">
			                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_email['createusertouser']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[createusertouser][status]" value="1" <?php echo (!empty($ticketsetting_email['createusertouser']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_enabled; ?>
			                      </label>
			                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_email['createusertouser']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[createusertouser][status]" value="0" <?php echo (empty($ticketsetting_email['createusertouser']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_disabled; ?>
			                      </label>
			                    </div>
			                	</div>
		              		</div>
		              		<div class="form-group mp-buttons">
		              			<label class="col-sm-2 control-label"><?php echo $emailtab_forgotpasswordtouser; ?></label>
			                	<div class="col-md-4 col-sm-5">
			                    <div class="btn-group btn-group-justified" data-toggle="buttons">
			                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_email['forgotpasswordtouser']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[forgotpasswordtouser][status]" value="1" <?php echo (!empty($ticketsetting_email['forgotpasswordtouser']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_enabled; ?>
			                      </label>
			                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_email['forgotpasswordtouser']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[forgotpasswordtouser][status]" value="0" <?php echo (empty($ticketsetting_email['forgotpasswordtouser']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_disabled; ?>
			                      </label>
			                    </div>
			                	</div>
		              		</div>
              			</div>

              			<div class="tab-pane" id="tab-email_admin">
                			<div class="form-group mp-buttons">
		              			<label class="col-sm-2 control-label"><?php echo $emailtab_createtickettoadmin; ?></label>
			                	<div class="col-md-4 col-sm-5">
			                    <div class="btn-group btn-group-justified" data-toggle="buttons">
			                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_email['createtickettoadmin']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[createtickettoadmin][status]" value="1" <?php echo (!empty($ticketsetting_email['createtickettoadmin']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_enabled; ?>
			                      </label>
			                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_email['createtickettoadmin']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[createtickettoadmin][status]" value="0" <?php echo (empty($ticketsetting_email['createtickettoadmin']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_disabled; ?>
			                      </label>
			                    </div>
			                	</div>
		              		</div>
		              		<div class="form-group mp-buttons">
		              			<label class="col-sm-2 control-label"><?php echo $emailtab_userreplytoadmin; ?></label>
			                	<div class="col-md-4 col-sm-5">
			                    <div class="btn-group btn-group-justified" data-toggle="buttons">
			                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_email['userreplytoadmin']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[userreplytoadmin][status]" value="1" <?php echo (!empty($ticketsetting_email['userreplytoadmin']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_enabled; ?>
			                      </label>
			                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_email['userreplytoadmin']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[userreplytoadmin][status]" value="0" <?php echo (empty($ticketsetting_email['userreplytoadmin']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_disabled; ?>
			                      </label>
			                    </div>
			                	</div>
		              		</div>
		              		<div class="form-group mp-buttons">
		              			<label class="col-sm-2 control-label"><?php echo $emailtab_createusertoadmin; ?></label>
			                	<div class="col-md-4 col-sm-5">
			                    <div class="btn-group btn-group-justified" data-toggle="buttons">
			                      <label class="mod-status btn btn-primary <?php echo !empty($ticketsetting_email['createusertoadmin']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[createusertoadmin][status]" value="1" <?php echo (!empty($ticketsetting_email['createusertoadmin']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_enabled; ?>
			                      </label>
			                      <label class="mod-status btn btn-primary <?php echo empty($ticketsetting_email['createusertoadmin']['status']) ? 'active' : ''; ?>">
			                        <input type="radio" name="ticketsetting_email[createusertoadmin][status]" value="0" <?php echo (empty($ticketsetting_email['createusertoadmin']['status'])) ? 'checked="checked"' : ''; ?> />
			                        <?php echo $text_disabled; ?>
			                      </label>
			                    </div>
			                	</div>
		              		</div>
                		</div>
                	</div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-support">
              <fieldset>
                <div class="form-group">
                  <div class="col-md-12 col-xs-12">
                    <h4 class="text-mpsuccess text-center"><i class="fa fa-thumbs-up" aria-hidden="true"></i> Thanks For Choosing Our Extension</h4>
                     <ul class="list-group">
                      <li class="list-group-item clearfix">Installed Version <span class="badge"><i class="fa fa-gg" aria-hidden="true"></i> V.1.0</span></li>
                    </ul>
                    <h4 class="text-mpsuccess text-center"><i class="fa fa-phone" aria-hidden="true"></i> Please Contact Us In Case Any Issue OR Give Feedback!</h4>
                    <ul class="list-group">
                      <li class="list-group-item clearfix">support@modulepoints.com <span class="badge"><a href="mailto:support@modulepoints.com?Subject=Request Support: Ticket System Extension"><i class="fa fa-envelope"></i> Contact Us</a></span></li>
                    </ul>
                  </div>
                </div>
              </fieldset>
            </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('.header-status').click(function() {
if($(this).find('input').val() == 1) {
  $('.headerfooter').removeClass('hide');
} else {
  $('.headerfooter').addClass('hide');
}
});


$('#language a:first').tab('show');
$('#ticketsubmit-language a:first').tab('show');
$('#ticketsuccess-language a:first').tab('show');
$('#footer-language a:first').tab('show');
$('#createtickettouser-language a:first').tab('show');

$('#panels a:first').tab('show');
$('#email-panels a:first').tab('show');
$('#lang-panels a:first').tab('show');
//--></script>
<?php if(VERSION <= '2.2.0.0') { ?>
<?php foreach ($languages as $language) { ?>
<script type="text/javascript"><!--
  $('#input-success-notlogged-description<?php echo $language['language_id']; ?>').summernote({ height: 300 });
  $('#input-success-logged-description<?php echo $language['language_id']; ?>').summernote({ height: 300 });
  $('#input-footer<?php echo $language['language_id']; ?>').summernote({ height: 300 });
//--></script>
<?php } ?>
<?php } else if (VERSION <= '2.3.0.2') { ?>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php } else { ?>
<link href="view/javascript/codemirror/lib/codemirror.css" rel="stylesheet" />
<link href="view/javascript/codemirror/theme/monokai.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="view/javascript/codemirror/lib/xml.js"></script>
<script type="text/javascript" src="view/javascript/codemirror/lib/formatting.js"></script>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/summernote-image-attributes.js"></script>
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<?php } ?>
</div>
<?php echo $footer; ?>