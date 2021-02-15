<?php
/**
 * Author: --=={{ *** MARSEL ALAMDAR *** }}==--
 * Date: 28/08/2017
 * Test APP For BeeJee
 *
 *
 * Class TaskController
 *
 */

class TaskController extends Controller
{
	public $params;

	private $items_per_page = 3;

	/**
	*
	* function start
	*
	*/
	public function start($params)
	{
		$this->params = $params;

		switch ($params[0]) {

			case "list":
			    $this->taskList();
			    break;

			case "view":
			    $this->taskView($params[1]);
			    break;

			case "opened":
			    $this->taskOpened();
			    break;

			case "closed":
			    $this->taskClosed();
			    break;

			case "create":
			    $this->taskCreate();
			    break;

			case "edit":
			    $this->taskEdit($params[1]);
			    break;

			case "delete":
			    $this->taskDelete($params[1]);
			    break;

			default:
			    $this->taskList();

		}

	}

	/**
	*
	* function taskList
	*
	*/
	public function taskList()
	{
		$taskModel = new TaskModel();
		$userModel = new UserModel();

		$order = "";
		$page = 1;
		$url = "/task/list/page/1/order/create-desc";

		// $url_arr = array();

		foreach($this->params as $i => $param) {
			
			if($param == 'order') {
				$order = $this->params[$i+1];
				// $url_arr[$param] = $order;
			}

			if($param == 'page') {
				$page = intval($this->params[$i+1]);
				// $url_arr[$param] = $page;
			}
		}

		// foreach($url_arr as $key => $val) $url .= "/" . $key . "/" . $val;

		$tasks_num = $taskModel->countTasks();
		$pages_num = ceil($tasks_num / $this->items_per_page);
		$page_start = ($page - 1) * $this->items_per_page;

		$tasks = $taskModel->getTasks($order, $page_start, $this->items_per_page);

		// HTML head
		$this->head = array(
		    'title' => "Задачи",
		    'description' => "Список задач",
		);

		$this->data['tasks'] = $tasks;
		$this->data['page_title'] = "Задачи";
		$this->data['tasks_num'] = $tasks_num;
		$this->data['pages_num'] = $pages_num;
		$this->data['page'] = $page;
		$this->data['order'] = $order;
		$this->data['url'] = $url . "/page/" . $page;
		$this->data['is_admin'] = $userModel->isAdmin();

		$this->view = 'task/list';
	}

	/**
	*
	* function taskView
	*
	*/
	public function taskView($task_id)
	{
		if (!empty($task_id)) {

			$taskModel = new TaskModel();

			// Retrieving an task by ID
			$task = $taskModel->getTask($task_id);

			// If no task was found we redirect to the ErrorController
			if (!$task) {
				$this->redirect('error');
			}

			// HTML head
			$this->head = array(
				'title' => $task['title'],
				'description' => $task['content'],
			);

			$task['closed'] = (strtotime($task['closed']) > 0) ? $task['closed'] : "";
			$task['create'] = (strtotime($task['create']) > 0) ? $task['create'] : "";
			$task['update'] = (strtotime($task['update']) > 0) ? $task['update'] : "";

			// Setting template variables
			$this->data = $task;

			// Setting the template
			$this->view = 'task/view';

		} else {
	
			$this->redirect('error');
	
		}
	
	}

	/**
	*
	* function taskCreate
	*
	*/
	public function taskCreate()
	{

		ini_set('upload_max_filesize', '15M');

		// Form submit
		if (!empty($_POST))
		{
			$taskModel = new TaskModel();



			$taskModel->addTask($_POST, $_FILES);
			$this->redirect('task/list');
		}

		// HTML head
		$this->head = array(
		    'title' => "Новая задача",
		    'description' => "Создание новой задачи",
		);

		// $this->data['tasks'] = $tasks;
		$this->data['page_title'] = "Новая задача";
		$this->data['url'] = "/task/create";

		$this->view = 'task/create';
	}

	/**
	*
	* function taskEdit
	*
	*/
	public function taskEdit($task_id)
	{

		$taskModel = new TaskModel();
		
		// Retrieving an task by ID
		$task = $taskModel->getTask($task_id);

		// Form submit
		if (!empty($_POST))
		{
			// var_dump(empty($_FILES['input_img']['tmp_name']));die;

			$taskModel->updateTask($_POST, $_FILES);
			$this->redirect('task/list');
		}

		// HTML head
		$this->head = array(
		    'title' => "Редактирование задача",
		    'description' => "Редактирование задачи",
		);

		// $this->data['tasks'] = $tasks;
		$this->data['page_title'] = "Новая задача";
		$this->data['url'] = "/task/edit";
		$this->data['task'] = $task;

		$this->view = 'task/edit';
	}

	/**
	*
	* function taskDelete
	*
	*/
	public function taskDelete($task_id)
	{

		$taskModel = new TaskModel();

		if(!empty($task_id)) {
			$taskModel->deleteTask($task_id);
		}

		$this->redirect('task/list');
	}
}
