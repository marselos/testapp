<?php
/**
 * Author: --=={{ *** MARSEL ALAMDAR *** }}==--
 * Date: 28/08/2017
 * Test APP For BeeJee
 *
 *
 * Class UserController
 *
 */

class UserController extends Controller
{

	public $params;

	/**
	*
	* function start
	*
	*/
	public function start($params)
	{
		$this->params = $params;

		switch ($params[0]) {

			case "login":
			    $this->userLogin();
			    break;

			case "logout":
			    $this->userLogout();
			    break;

			default:
			    $this->userLogin();

		}

	}

	/**
	*
	* function userLogin
	*
	*/
	public function userLogin()
	{
		$userModel = new UserModel();
		$url = "/user/login";

		// Form submit
		if (!empty($_POST))
		{
			if($userModel->doLogin($_POST)) {
				$_SESSION['auth'] = 1;
				$this->redirect('task/list');
			} else {
				$this->data['error'] = "notauth";
			}
		}


		// HTML head
		$this->head = array(
		    'title' => "Вход",
		    'description' => "Вход в панель управления",
		);

		$this->data['page_title'] = "Вход";
		$this->data['url'] = $url;

		$this->view = 'user/login';
	}

	/**
	*
	* function userLogin
	*
	*/
	public function userLogout()
	{
		unset($_SESSION['auth']);
		$this->redirect('task/list');
	}

}