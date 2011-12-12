<?php
/* API to use an Identi.ca account from Wordpress.
 * by Moises Brenes <moises.brenes@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation.
 *
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
class MyIdentica {
	private $user; // screen name
	private $api_user_timeline; // user timeline endpoint
	private $api_user_profile; // profile endpoint

	const USER_URL = 'http://identi.ca/user/'; // user endpoint base
	const TAG_URL = 'http://identi.ca/tag/'; // tag endpoint base
	const GROUP_URL = 'http://identi.ca/group/'; // group endpoint base
	const NOTICE_URL = 'http://identi.ca/notice/'; // notice endpoint base
	const API_URL = 'http://identi.ca/api/'; // api endpoint base
	const API_COUNT = 16; // default amount of statuses to fetch
	const USER_AGENT = 'MyIdentica/1.0.0.alpha;http://mbrenes.com'; // custom user-agent

	/**
	 * PHP5 constructor
	 *
	 * @param string $user identi.ca screen name
	 */
	public function __construct($user) {
		$this->user = (string) $user;
		$this->api_user_timeline = sprintf("%sstatuses/user_timeline/", MyIdentica::API_URL);
		$this->api_user_profile = sprintf("%susers/show/", MyIdentica::API_URL);
	}

	/**
	 * Make requests to retrieve json responses
	 *
	 * @param string $endpoint full endpoint url
	 * @return array set of statuses as objects
	 */
	private function request($endpoint) {
		$_endpoint = (string) $endpoint;
		$response = wp_remote_get($endpoint, array('method' => 'GET', 'timeout' => 4, 'redirection' => 5, 'httpversion' => 1.0, 'blocking' => true, 'user-agent' => MyIdentica::USER_AGENT));
		if (is_wp_error($response) == false) {
			if ((wp_remote_retrieve_response_code($response) == 200) && function_exists('json_decode'))
				return empty($response['body']) ? array() : json_decode($response['body']);
		}
		return array();
	}

	/** Get user statuses
	 *
	 * @param int $count Optional amount of statuses to fetch
	 * @return array set of statuses as objects
	 */
	public function get_user_timeline($count=MyIdentica::API_COUNT) {
		try {
			$_count = (int) $count;
		} catch (Exception $e) {
			$_count = MyIdentica::API_COUNT;
		}
		$count = ($_count < 1) ? MyIdentica::API_COUNT : $_count;
		$endpoint = sprintf("%s%s.json?count=%d", $this->api_user_timeline, $this->user, $count);
		return $this->request($endpoint);
	}

	/** Get profile information
	 *
	 * @return mixed user data as object
	 */
	public function get_user_profile() {
		$endpoint = sprintf("%s%s.json", $this->api_user_profile, $this->user);
		return $this->request($endpoint);
	}
}
