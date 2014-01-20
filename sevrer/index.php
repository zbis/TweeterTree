	<?php require_once('functions.php'); ?>
	<html lang="fr">
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="Cloe DUC - Faclab.org">
	<link rel="shortcut icon" href="">

	<title>Arbre à Tweets - Configuration</title>

	<!-- Bootstrap core CSS -->
	<link href="assets/css/bootstrap.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="style.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  <style type="text/css"></style>
  </head>

  <body style="">

    <!-- Wrap all page content here -->
    <div id="wrap">

      <!-- Fixed navbar -->
      <div class="navbar navbar-default navbar-fixed-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Arbre à Tweets</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Arbre à Tweets</a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="#savetree">Enregistrer ou modifier un arbre</a></li>
					<li><a href="#doc">Documentation</a></li>
					<li><a href="#stats">Statistiques</a></li>
				</ul>
			</div><!--/.nav-collapse -->
		</div>
      </div>

      <!-- Begin page content -->
      <div class="container">
		<div class="jumbotron">
	        <h1>Arbre à Tweets</h1>
	        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit. Donec et mollis dolor. Praesent et diam eget libero egestas mattis sit amet vitae augue. Nam tincidunt congue enim, ut porta lorem lacinia consectetur. Donec ut libero sed arcu vehicula ultricies a non tortor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ut gravida lorem. Ut turpis felis, pulvinar a semper sed, adipiscing id dolor. Pellentesque auctor nisi id magna consequat sagittis. Curabitur dapibus enim sit amet elit pharetra tincidunt feugiat nisl imperdiet. Ut convallis libero in urna ultrices accumsan. Donec sed odio eros. Donec viverra mi quis quam pulvinar at malesuada arcu rhoncus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In rutrum accumsan ultricies. Mauris vitae nisi at sem facilisis semper ac in est.</p>
      	</div>

	        <div class="page-header">
	          <h2 id="savetree">Enregistrer ou modifier un arbre</h2>
	        </div>
			<form id="shieldForm" role="form" action="api.php?domain=register", method="POST">
				<fieldset>
					<h3>Identification</h3>
					<div class="form-group">
						<input type="text" class="form-control"  placeholder="Identifiant du shield" required="true" id="shield_id" name="shield_id">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" placeholder="Mot de passe" required="" id="password" name="password">
					</div>
					<button name="create" type="button" class="btn btn-default">Créer mon arbre</button>
					<p class="help-block">Choisissez arbitrairement un couple ID/Password, puis notez le. Il vous permettera de revenir modifier la configuration de votre arbre</p>
				</fieldset>
				<fieldset>
					<h3 class="form-signin-heading">Configuration</h3>
					<div class="form-group">
						<label for="led_count" class="col-sm-8 control-label">Nombre de leds sur le montage :</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" placeholder="Par défaut : <?php echo default_conf('led_count'); ?>" id="led_count" name="led_count">
						</div>
					</div>
					<div class="form-group">
						<label for="blinking_time" class="col-sm-8 control-label" >Temps d'allumage : </label>
						<div class="col-sm-4">
							<input type="text" class="form-control" placeholder="Par défaut : <?php echo default_conf('blinking_time'); ?>" id="blinking_time" name="blinking_time">
						</div>
					</div>
					<div class="form-group">
						<label for="hastags" class="col-sm-12 control-label" >Hastag(s) suivi(s) : </label>
						<div class="col-sm-12">
							<input type="text" class="form-control" placeholder="Par défaut : <?php echo default_conf('hastags'); ?>" id="hastags" name="hastags">
						</div>
						<p class="help-block">Renseignez le ou les hastag que vous voulez suivre de la manière suivante : #hastag1, #hastag2, (etc)</p>
					</div>
				</fieldset>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Enregistrer</button>
			</form>
      </div>
    </div>

    <div id="footer">
      <div class="container">
        <p class="text-muted">Place sticky footer content here.</p>
      </div>
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="../../dist/js/bootstrap.min.js"></script>
  
<script>
	    $(document).ready(function(){
	    	$('button[name=create]').click(function(){
	    		var form = $(this).parent().parent('form');
	    		var datas = form.serialize();
				jQuery.ajax({
	                url: "api.php?domain=register",
	                type: "POST",
	                data: datas,
	                dataType : 'json',
	                success: function( response ) {
	                	displayNotifications(form, response.notifications);
	                }
            	});
            	return false;
	    	});
	    	$('input#password').blur(function(){
	    		identify_shield();
	    	});
	    	$('input#shield_id').blur(function(){
	    		identify_shield();
	    	});
	    	$('form').submit(function(){
	    		var form = $(this);
	    		var datas = $(this).serialize();
				jQuery.ajax({
	                url: "api.php?domain=update",
	                type: "POST",
	                data: datas,
	                dataType : 'json',
	                success: function( response ) {
	                	displayNotifications(form, response.notifications);
	                }
            	});
	    		return false;
	    	});
	    	function identify_shield()
	    	{
	    		var id = $('input#shield_id').val();
	    		var password = $('input#password').val();
	    		var form = $("form#shieldForm");
	    		if( id != '' && password != '')
	    		{
					jQuery.ajax({
		                url: "api.php?domain=shield",
		                type: "GET",
		                data: {shield_id : id, password : password},
		                dataType : 'json',
		                success: function( response ) {
		                	if(response.items){
			                	if(response.items.autorisation == true)
			                	{
			                		$('input#led_count').val(response.items[0].led_count);
			                		$('input#blinking_time').val(response.items[0].blinking_time);
			                		$('input#hastags').val(response.items[0].hashtags);
			                	}
		                	}
		                	displayNotifications(form, response.notifications);
		                }
	            	});
	    		}
	    	}
	    	function displayNotifications(form, response)
	    	{
	    		$('.alert').fadeOut();
	    		$('.alert').remove();
	        	 for(var key in response){
	        	 		var obj = response[key];
			          form.prepend('<div class="alert alert-'+obj.typealert+' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+obj.message+'</div>');
			      }
	    	}
	    })
	    </script>
</body>
</html>