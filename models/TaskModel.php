<?php
/**
 * Author: --=={{ *** MARSEL ALAMDAR *** }}==--
 * Date: 28/08/2017
 * Test APP For BeeJee
 *
 * 
 * Class TaskModel
 * 
 */
class TaskModel
{

	private $order_names = array(
		"title" => "t_title",
		"user" => "t_user",
		"create" => "t_date_create",
		"update" => "t_date_update",
		"closed" => "t_date_closed",
	);

	private $order_directs = array(
		"asc" => "asc",
		"desc" => "desc",
	);

	private $res_per_page = 3;

	/**
	 * Returns an task from the database by ID
	 * @param string $id Task ID
	 * @return array | false The task or false if not found
	 */
	public function getTask($id)
	{
		return Db::queryOne('
			SELECT 
				`t_id` AS `id`, 
				`t_user` AS `user`,
				`t_email` AS `email`,
				`t_title` AS `title`, 
				`t_content` AS `content`,
				`t_img` AS `img`,
				`t_status` AS `status`,
				DATE_FORMAT(`t_date_create`,"%d.%m.%Y %H:%i") AS `create`,
				DATE_FORMAT(`t_date_update`,"%d.%m.%Y %H:%i") AS `update`,
				DATE_FORMAT(`t_date_closed`,"%d.%m.%Y %H:%i") AS `closed`

			FROM `t_tasks`
			WHERE `t_id` = ?
		', array($id));
	}

	/**
	 * Count Tasks
	 * @return integer Number of tasks in database
	 */
	public function countTasks()
	{
		return Db::query('SELECT `t_id` FROM `t_tasks`');	
	}


	/**
	 * Returns a list of all tasks in the database
	 * @return array All the tasks in the database
	 */
	public function getTasks($order = array(), $pages_start = 0, $pages_num = 1)
	{
		// Default order
		$order_sql = "ORDER BY `t_date_create` DESC";

		if(!empty($order)) {
			list($order_name, $order_direct) = explode("-", $order);

			if(isset($this->order_names[$order_name]) && isset($this->order_directs[$order_direct])) {
				$order_sql = "ORDER BY " . $this->order_names[$order_name] . " " . $this->order_directs[$order_direct];	
			}

		}

		// Pagination
		$limit_sql = "LIMIT " . $pages_start . "," . $pages_num;

		return Db::queryAll('
			SELECT 
				`t_id` AS `id`, 
				`t_title` AS `title`, 
				`t_content` AS `content`,
				`t_status` AS `status`,
				DATE_FORMAT(`t_date_create`,"%d.%m.%Y %H:%i") AS `create`,
				DATE_FORMAT(`t_date_update`,"%d.%m.%Y %H:%i") AS `update`,
				DATE_FORMAT(`t_date_closed`,"%d.%m.%Y %H:%i") AS `closed`,
				`t_user` AS `user`,
				`t_email` AS `email`

			FROM `t_tasks`
		' . $order_sql . ' ' . $limit_sql);
	}

	/**
	 * Returns a list of all tasks in the database
	 * @return array All the tasks in the database
	 */
	public function addTask($data)
	{

		// Upload Image
		if(!empty($_FILES)) {

			$uploaddir = IMG;

			$file_parts = pathinfo($_FILES['input_img']['name']);
			$file_ext = strtolower($file_parts['extension']);
			$file_name = md5(microtime()) . "." . $file_ext;
			$file_path = $uploaddir . $file_name ;

			if (move_uploaded_file($_FILES['input_img']['tmp_name'], $file_path)) {

				// Resize Image
				$img = new Imaging;
				$img->set_img($file_path);
				$img->set_size(320);
				$img->save_img($file_path);

				// Finalize
				$img->clear_cache();

			}		
		}


		// Validate data
		foreach($data as $id => $val) {

			$val = trim($val);
			$val = strip_tags($val);
			$val = htmlspecialchars($val);

			$data[$id] = $val;
		}

		return Db::query('
			INSERT INTO `t_tasks`
			SET
				`t_user` = "'.$data['input_user'].'",
				`t_email` = "'.$data['input_email'].'",
				`t_title` = "'.$data['input_title'].'",
				`t_content` = "'.$data['input_content'].'",
				`t_img` = "'.$file_name.'",
				`t_status` = "0",
				`t_date_create` = NOW(),
				`t_date_update` = NOW()
		');		

	}

	/**
	 * Returns a list of all tasks in the database
	 * @return array All the tasks in the database
	 */
	public function updateTask($data)
	{

		$file_name = "";

		// Upload Image
		if(!empty($_FILES['input_img']['tmp_name'])) {

			$uploaddir = IMG;

			$file_parts = pathinfo($_FILES['input_img']['name']);
			$file_ext = strtolower($file_parts['extension']);
			$file_name = md5(microtime()) . "." . $file_ext;
			$file_path = $uploaddir . $file_name ;

			if (move_uploaded_file($_FILES['input_img']['tmp_name'], $file_path)) {

				// Resize Image
				$img = new Imaging;
				$img->set_img($file_path);
				$img->set_size(320);
				$img->save_img($file_path);

				// Finalize
				$img->clear_cache();

			}		
		}

		$sql_img = (!empty($file_name)) ? "`t_img` = '".$file_name."'," : "";

		// echo $sql_img;die;

		// Validate data
		foreach($data as $id => $val) {

			$val = trim($val);
			$val = strip_tags($val);
			$val = htmlspecialchars($val);

			$data[$id] = $val;
		}

		$data['input_status'] = (isset($data['input_status'])) ? 1 : 0;

		if($data['input_status']) {
			$sql_date_closed = "`t_date_closed` = NOW()";
		} else {
			$sql_date_closed = "`t_date_closed` = ' '";
		}

		return Db::query('
			UPDATE `t_tasks`
			SET
				`t_user` = "'.$data['input_user'].'",
				`t_email` = "'.$data['input_email'].'",
				`t_title` = "'.$data['input_title'].'",
				`t_content` = "'.$data['input_content'].'",
				'.$sql_img.' 
				`t_status` = "'.$data['input_status'].'",
				`t_date_update` = NOW(),
				'.$sql_date_closed.'

			WHERE `t_id` = "'.$data['input_id'].'"
		');		

	}

	/**
	 * deleteTask
	 * 
	 */
	public function deleteTask($task_id)
	{
		return Db::query('
			DELETE FROM `t_tasks`
			WHERE `t_id` = "'.$task_id.'"
		');		

	}

}