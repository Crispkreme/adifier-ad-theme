<?php
if( !class_exists( 'Adifier_Recaptcha' ) )
{
    class Adifier_Recaptcha
    {
        protected $site_key;
        protected $secret_key;

        public function __construct()
        {
            add_action( 'init',                    [$this, 'init']    );
        }

        public function init()
        {
            $this->site_key   = adifier_get_option( 'recaptcha_site_key' );
            $this->secret_key = adifier_get_option( 'recaptcha_secret_key' );

            if( !empty( $this->site_key ) && !empty( $this->secret_key ) )
            {
                add_action( 'wp_enqueue_scripts',      [$this, 'scripts'] );
                add_filter( 'adifier_verify_captcha',  [$this, 'verify_captcha'], 10, 2);
                add_action( 'adifier_recaptcha_field', [$this, 'recaptcha_field'], 10 , 1);
            }
        }

        public function scripts()
        {
            wp_enqueue_script( 'recaptcha', '//www.google.com/recaptcha/api.js?render=' . $this->site_key, [], false, true );
            wp_localize_script( 
                'recaptcha', 
                'reCaptchaData',
                [
                    'siteKey' => $this->site_key
                ]
            );
        }

        public function verify_captcha( $result, $captcha = 'recaptcha' )
        {
            $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array(
                'timeout' 		=> 45,
                'redirection' 	=> 5,
                'httpversion' 	=> '1.0',
                'blocking' 		=> true,
                'body'          => [
                    'secret'        => $this->secret_key,
                    'response'      => $_REQUEST[ $captcha ] ?? ''
                ] 
            ));
    
            $result = false;
            if ( !is_wp_error( $response ) )
            {
                $response = json_decode( $response['body'] );
                $result = $response->success == true && $response->score > 0.5 ? true : false;
            }          

            return $result;
        }


        public function recaptcha_field()
        {
            ?>
            <input type="hidden" class="recaptcha" name="recaptcha" />
            <?php
        }
    }
    
    new Adifier_Recaptcha();
}
?>