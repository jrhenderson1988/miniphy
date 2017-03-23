<!doctype html><html lang="en"><head><meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?php echo $__env->yieldContent('title', 'Title of page'); ?> | Website.</title><meta name="description" content="<?php echo $__env->yieldContent('description', 'Default description.'); ?>" /><meta name="keywords" content="<?php echo $__env->yieldContent('keywords', 'Default keywords'); ?>"><meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"><?php if (! empty(trim($__env->yieldContent('robots')))): ?><meta name="robots" content="<?php echo $__env->yieldContent('robots'); ?>"><?php endif; ?><!--[if lt IE 9]><script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script><script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]--><link href="https://fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"><link rel="stylesheet" type="text/css" href="<?php echo e(asset(mix('css/app.css'))); ?>"></head><body><script>
    window.fbAsyncInit = function() {
        FB.init({
            appId: '<?php echo e(config('services.facebook.client_id')); ?>',
            xfbml: true,
            version: 'v2.8'
        });
        FB.AppEvents.logPageView();
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script><script>
    window.twttr = (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
            t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);

        t._e = [];
        t.ready = function(f) {
            t._e.push(f);
        };

        return t;
    }(document, "script", "twitter-wjs"));
</script><div class="site-layout"><div class="site-layout__header"><?php echo $__env->make('layouts.partials.site-header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></div><?php echo $__env->make('layouts.partials.site-drawer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?><div class="site-layout__content"><?php echo $__env->yieldContent('content-area'); ?></div><div class="site-layout__footer"><?php echo $__env->make('layouts.partials.site-footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?></div></div><script src="<?php echo e(asset(mix('js/app.js'))); ?>" async></script><?php if(config('app.env') == 'production'): ?><script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-XXXXXXXX-1', 'auto');
        ga('send', 'pageview');
    </script><?php endif; ?></body></html>
