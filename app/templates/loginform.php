<?php
use \core\MVCF;
$app = MVCF::app();
$imgDir = '/' . $app->baseDir . '/assets/images';
?>
<section id="login-form">
    <div class="container">

        <div class="row">
            <div class="col-xs-12">
                <div class="form-wrap">
                    <div class="logo-enter"><img src="<?php echo $imgDir; ?>/logot.png" alt=""></div>
                    <form role="form" action="" method="post" id="login-form" autocomplete="off">
                        <div class="form-group">
                            <label for="email" class="sr-only">Логин</label>
                            <input type="email" name="login" id="login" class="form-control" placeholder="Логин">
                        </div>
                        <div class="form-group enter">
                            <label for="key" class="sr-only">Пароль</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Пароль">

                        <div class="checkbox">
                            <span class="character-checkbox" onclick="showPassword()"> <i class="fa fa-eye"  ></i></span>
                        </div>
                        </div>
                        <input type="submit" id="btn-login" class="btn btn-custom btn-lg btn-block" name="submit" value="Войти">
                    </form>
                </div>
            </div> <!-- /.col-xs-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</section>
