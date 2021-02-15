<?php
/**
 * Author: --=={{ *** MARSEL ALAMDAR *** }}==--
 * Date: 28/08/2017
 * Test APP For BeeJee
 *
 * 
 * Class UserModel
 * 
 */
class UserModel
{

	/**
	 * Auth
	 * @param array $params
	 * @return
	 */
	public function doLogin($data)
	{
		if(!empty($data)) {
			$data = $this->checkInput($data);

			return Db::queryOne('
				SELECT *
				FROM `u_user`
				WHERE `u_user` = ?
				AND `u_password` = ?
			', array($data['input_user'], md5($data['input_password'])));
		} else {
			return false;
		}
	}

	public function checkInput($data)
	{

		// Validate data
		foreach($data as $id => $val) {

			$val = trim($val);
			$val = strip_tags($val);
			$val = htmlspecialchars($val);

			$data[$id] = $val;
		}

		return $data;

	}

	/**
	*
	* function isAdmin
	*
	*/
	public function isAdmin()
	{
		return (isset($_SESSION['auth']) && $_SESSION['auth'] == 1) ? true : false;
	}
	
}