<header id="header">
    {*
    <div id="logo-group">
        <span id="logo"> <img src="img/logo.png" alt="SmartAdmin"> </span>
    </div>

    <span id="extr-page-header-space"> <span class="hidden-mobile hiddex-xs">Need an account?</span> <a
            href="register.html" class="btn btn-danger">Create account</a> </span>
    *}
</header>

<div id="main" role="main" style="margin-left: 0;">
    <div id="content" class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-4">
            </div>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
                <div class="well no-padding">
                    <form method="post" action="" id="login-form" class="smart-form client-form">
                        <header>{lang txt="Sign In"}</header>

                        <fieldset>

                            <section>
                                <label class="label">{lang txt="E-mail"}/{lang txt="Username"}</label>
                                <label class="input">
                                    <i class="icon-append fa fa-user"></i>
                                    <input type="text" name="username">
                                    <b class="tooltip tooltip-top-right">
                                        <i class="fa fa-user txt-color-teal"></i> {lang txt="Please enter email address/username"}</b>
                                </label>
                            </section>

                            <section>
                                <label class="label">{lang txt="Password"}</label>
                                <label class="input"> <i class="icon-append fa fa-lock"></i>
                                    <input type="password" name="password">
                                    <b class="tooltip tooltip-top-right">
                                        <i class="fa fa-lock txt-color-teal"></i> {lang txt="Enter your password"}
                                    </b>
                                </label>
                                {*
                                <div class="note">
                                    {anchor txt="Forgot password"}
                                </div>
                                *}
                            </section>
                            {*
                            <section>
                                <label class="checkbox">
                                    <input type="checkbox" name="remember" checked="">
                                    <i></i>{lang txt="Stay signed in"}</label>
                            </section>
                            *}
                        </fieldset>
                        <footer>
                            {if isset($google_login_url)}
                            <a class="btn btn-primary" href="{$google_login_url}">{lang txt="Google Login"}</a>
                            {/if}
                            <button type="submit" class="btn btn-primary">{lang txt="Sign In"}</button>
                        </footer>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    runAllForms();
    $(function () {
        // Validation
        $("#login-form").validate({
            // Rules for form validation
            rules: {
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 3,
                    maxlength: 20
                }
            },

            // Messages for form validation
            messages: {
                email: {
                    required: '{lang txt="Please enter your email address"}',
                    email: '{lang txt="Please enter a VALID email address"}'
                },
                password: {
                    required: '{lang txt="Please enter your password"}'
                }
            },

            // Do not change code below
            errorPlacement: function (error, element) {
                error.insertAfter(element.parent());
            }
        });
    });
</script>