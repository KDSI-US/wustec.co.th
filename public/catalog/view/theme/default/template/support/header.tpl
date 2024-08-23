<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content= "<?php echo $keywords; ?>" />
<?php } ?>
<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/modulepoints/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />
<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="catalog/view/javascript/common.js" type="text/javascript"></script>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($analytics as $analytic) { ?>
<?php echo $analytic; ?>
<?php } ?>
</head>
<body class="<?php echo $class; ?>">
<header>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
        <div id="logo">
          <?php if ($logo) { ?>
          <a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $store_name; ?>" alt="<?php echo $store_name; ?>" class="img-responsive" /></a>
          <?php } else { ?>
          <h1><a href="<?php echo $home; ?>"><?php echo $store_name; ?></a></h1>
          <?php } ?>
        </div>
      </div>
      <?php if($submission_link_display || $login_link_display) { ?>
      <div class="col-sm-5 pull-right text-right">
        <ul class="list-inline ticket-links">
          <?php if($submission_link_display) { ?>
          <li><a href="<?php echo $link_ticket_submission; ?>"><?php echo $button_ticket_submission; ?></a></li>
          <?php } ?>

          <?php if($login_link_display) { ?>
            <?php if($logged) { ?>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown">
                <?php if($userphoto_display_header) { ?>
                <img src="<?php echo $photo_thumb; ?>" alt="<?php echo $name; ?>" class="img-circle" /> 
                <?php } ?>
                <span class=""><?php echo $name; ?></span> 
                <span class="caret"></span>
              </a>
            <ul class="dropdown-menu dropdown-menu-right">
              <li><a href="<?php echo $request_list; ?>"><?php echo $text_request_list; ?></a></li>
              <li><a href="" data-toggle="modal" data-target="#modalEditForm"><?php echo $text_edit_profile; ?></a></li>
              <li><a href="" data-toggle="modal" data-target="#modalEditPassword"><?php echo $text_edit_password; ?></a></li>
              <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
            </ul>
            <?php } else { ?>
            <li><a href="" data-toggle="modal" data-target="#modalLoginForm"><?php echo $button_support_login; ?></a></li>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>
    </div>
  </div>
  <?php if($logged) { ?>
  <div class="modal fade" id="modalEditForm" tabindex="-1" role="dialog">
    <div class="modal-dialog cascading-modal" role="document">
      <div class="modal-content">
        <div class="modal-header">
           <h4 class="modal-title"><?php echo $heading_edit_profile; ?></h4>
        </div>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group">
              <label class="control-label col-sm-2"><?php echo $entry_name; ?></label>
              <div class="col-sm-10">
                <input type="text" id="editticket-name" name="name" class="form-control" value="<?php echo $name; ?>" />
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2"><?php echo $entry_email; ?></label>
              <div class="col-sm-10">
                <input type="email" id="editticket-email" name="email" class="form-control" value="<?php echo $email; ?>" />
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-sm-2"><?php echo $entry_image; ?></label>
              <div class="col-sm-3">
                <a class="pic-upload">
                  <img src="<?php echo $ticketuser_thumb; ?>" class="img-responsive"/>
                  <input type="hidden" id="editticket-image" name="image" value="<?php echo $image; ?>" />
                </a>
              </div>
              <div class="col-sm-3">
                <button type="button" class="pic-upload-button btn btn-danger btn-sm"><?php echo $button_upload_image; ?></button>
              </div>
            </div>
            <div class="buttons text-right">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_close; ?></button>
              <button type="button" class="btn btn-primary btn-edituser"><?php echo $button_submit; ?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalEditPassword" tabindex="-1" role="dialog">
    <div class="modal-dialog cascading-modal" role="document">
      <div class="modal-content">
    		<div class="modal-header">
	         <h4 class="modal-title"><?php echo $heading_edit_password; ?></h4>
    		</div>
    		<div class="modal-body">
    			<div class="form-horizontal">
      			<div class="form-group">
              <label class="control-label col-sm-2"><?php echo $entry_password; ?></label>
              <div class="col-sm-10">
                <input type="password" id="editticket-password" name="password" class="form-control" value="" />
              </div>
            </div>
            <div class="form-group">
      				<label class="control-label col-sm-2"><?php echo $entry_password_confirm; ?></label>
      				<div class="col-sm-10">
      					<input type="password" id="editticket-confirm" name="confirm" class="form-control" value="" />
      				</div>
      			</div>
      			<div class="buttons text-right">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_close; ?></button>
            	<button type="button" class="btn btn-primary btn-editpassword"><?php echo $button_submit; ?></button>
            </div>
    			</div>
    		</div>
    	</div>
    </div>
  </div>
  <?php } ?>

  <?php if(!$logged) { ?>
  <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog cascading-modal" role="document">
      <div class="modal-content">
          <div class="modal-c-tabs">
              <ul class="nav nav-tabs tabs-2 light-blue darken-3" role="tablist">
                  <li class="nav-item waves-effect waves-light active">
                    <a class="nav-link panel-login" data-toggle="tab" href="#panel-login" role="tab">
                    <i class="fa fa-user mr-1"></i> <?php echo $tab_login; ?></a>
                  </li>
                  <li class="nav-item waves-effect waves-light">
                    <a class="nav-link panel-register" data-toggle="tab" href="#panel-register" role="tab">
                    <i class="fa fa-user-plus mr-1"></i> <?php echo $tab_register; ?></a>
                  </li>
              </ul>
              <div class="tab-content">
                  <div class="tab-pane fade in active" id="panel-login" role="tabpanel">
                      <div class="modal-body mb-1">
                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            <input type="email" class="form-control" name="email" value="" placeholder="<?php echo $entry_email; ?>">
                          </div>
                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" class="form-control" name="password" value="" placeholder="<?php echo $entry_password; ?>">
                          </div>
                          <div class="text-center mt-2">
                              <button class="btn btn-info waves-effect waves-light button-login-submit"><?php echo $button_login; ?>
                                  <i class="fa fa-sign-in ml-1"></i>
                              </button>
                          </div>
                      </div>
                      <div class="modal-footer display-footer">
                          <div class="options text-center text-md-right mt-1">
                              <p><?php echo $text_not_memeber; ?>
                                  <a class="blue-text open-register"><?php echo $text_signup; ?></a>
                              </p>
                              <p><?php echo $text_forgot; ?>
                                  <a class="blue-text open-forgot" data-toggle="tab" href="#panel-forgot" role="tab"><?php echo $text_password; ?></a>
                              </p>
                          </div>
                          <button type="button" class="btn btn-outline-info waves-effects ml-auto" data-dismiss="modal"><?php echo $button_close; ?></button>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="panel-forgot" role="tabpanel">
                      <div class="modal-body mb-1">
                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            <input type="email" class="form-control" name="email" value="" placeholder="<?php echo $entry_email; ?>">
                          </div>
                          <div class="text-center mt-2">
                              <button class="btn btn-info waves-effect waves-light button-forgot-submit"><?php echo $button_forgot; ?></button>
                          </div>
                      </div>
                      <div class="modal-footer display-footer">
                        <div class="options text-center text-md-right mt-1">
                          <p><?php echo $button_already; ?>
                              <a class="blue-text open-login" data-toggle="tab" href="#panel-login" role="tab"><?php echo $button_login; ?></a>
                          </p>
                        </div>
                        <button type="button" class="btn btn-outline-info waves-effects ml-auto" data-dismiss="modal"><?php echo $button_close; ?></button>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="panel-register" role="tabpanel">
                      <div class="modal-body">
                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="ticket-name" type="text" class="form-control" name="name" value="" placeholder="<?php echo $entry_name; ?>">     
                          </div>
                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            <input id="ticket-email" type="email" class="form-control" name="email" value="" placeholder="<?php echo $entry_email; ?>">     
                          </div>
                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="ticket-password" type="password" class="form-control" name="password" value="" placeholder="<?php echo $entry_password; ?>">
                          </div>
                          <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="ticket-confirm" type="password" class="form-control" name="confirm" value="" placeholder="<?php echo $entry_password_confirm; ?>">
                          </div>
                          <div class="text-center form-sm mt-2">
                              <button class="btn btn-info waves-effect waves-light button-register-submit"><?php echo $button_signup; ?> <i class="fa fa-sign-in ml-1"></i>
                              </button>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <div class="col-sm-6">
                            <div class="options text-left">
                                <p class="pt-1"><?php echo $button_already; ?>
                                    <a class="blue-text open-login"><?php echo $button_login; ?></a>
                                </p>
                            </div>
                          </div>
                          <div class="col-sm-6 text-right">
                            <button type="button" class="btn btn-outline-info waves-effect ml-auto" data-dismiss="modal"><?php echo $button_close; ?></button>
                          </div>  
                      </div>
                  </div>
              </div>
          </div>
      </div>
    </div>
  </div>  
  <script type="text/javascript">
  $(document).ready(function() {
    $('.open-register').click(function() {
      $('.panel-register').trigger('click');
    });
    $('.open-login').click(function() {
      $('.panel-login').trigger('click');
    });

    <?php if($login_popup) { ?>
    $('#modalLoginForm').modal('show');
    <?php } ?>
  });
  </script>
  <?php } ?>
</header>