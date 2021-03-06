
<meta charset="utf-8">
<title>{site_title_func}</title>
<meta name="description" content="{site_description_func}" />
<meta name="author" content="{site_author_func}" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="icon" type="image/png" href="/favicon.ico" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">

{php}
    /*
    <?php if (config_item('site_appleicon') != '') : ?>
    <link rel="apple-touch-icon" href="<?=base_url()?>resource/images/<?=config_item('site_appleicon')?>" />
    <link rel="apple-touch-icon" sizes="72x72" href="<?=base_url()?>resource/images/<?=config_item('site_appleicon')?>" />
    <link rel="apple-touch-icon" sizes="114x114" href="<?=base_url()?>resource/images/<?=config_item('site_appleicon')?>" />
    <link rel="apple-touch-icon" sizes="144x144" href="<?=base_url()?>resource/images/<?=config_item('site_appleicon')?>" />
    <?php endif; ?>

    <title><?php echo $template['title'];?></title>
    <meta name="description" content="<?=config_item('site_desc')?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?=base_url()?>resource/css/app.css" type="text/css" />
    <link rel="stylesheet" href="<?=base_url()?>resource/css/login.css" type="text/css" cache="false" />

    <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js" cache="false">
    </script>
    <script src="js/ie/respond.min.js" cache="false">
    </script>
    <script src="js/ie/excanvas.js" cache="false">
    </script> <![endif]-->
    */

{/php}
{assets type='css'}
{if isset($js_header)}
<script type="text/javascript" language="JavaScript">
    {$js_header}
</script>
{/if}
