<?php

namespace Inc\Controllers;

class AuthController extends BaseController {
	public function register() {
		if ( ! $this->activated( 'login_manager' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'wp_head', [ $this, 'add_auth_template' ] );
		add_action( 'wp_ajax_nopriv_peach_core_login', [ $this, 'login' ] );
	}

	public function add_auth_template() {
		// if user logged is
		if ( is_user_logged_in() ) {
			return;
		}

		$file = $this->plugin_path . 'templates/auth.php';

		if ( file_exists( $file ) ) {
			load_template( $file, true );
		}


	}

	public function enqueue() {
		// if user logged is
		if ( is_user_logged_in() ) {
			return;
		}
		wp_enqueue_style( 'authStyle', $this->plugin_url . 'assets/auth.css', [], null );
		wp_enqueue_script( 'authScript', $this->plugin_url . 'assets/auth.js', [], null );
	}


	public function login() {
		check_ajax_referer( 'ajax-login-nonce', 'peach_core_auth' );

		$info                  = [];
		$info['user_login']    = $_POST['username'];
		$info['user_password'] = $_POST['password'];
		$info['remember']      = true;

		// do the login
		// if secure cookie was false wp doubles check the login with its builtin login, if was true, user completely will be logged in
		$user_signon = wp_signon( $info, true );

		if ( is_wp_error( $user_signon ) ) {
			echo json_encode( [
				'status'  => false,
				'message' => 'نام کاربری یا کلمه عبور اشتباه است',
			] );

			die;
		}

		echo json_encode( [
			'status'  => true,
			'message' => 'با موفقیت وارد شدید. درحال انتقال...',
		] );

		die;

	}
}