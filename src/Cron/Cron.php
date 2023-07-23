<?php
/**
 * Abstract class for handling scheduled CRON tasks.
 *
 * This class provides a base for handling scheduled CRON tasks in WordPress.
 *
 * @since 1.0.0
 *
 * @package woo-paddle-gateway
 */

namespace Woo_Paddle_Gateway\Cron;

/**
 * CRON class.
 */
abstract class Cron {

	/**
	 * The unique name for the CRON task.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $task_name;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $task_name The unique name for the CRON task.
	 */
	public function __construct( $task_name ) {

		$this->task_name = sanitize_key( $task_name );
	}

	/**
	 * Schedule the CRON task.
	 *
	 * This method should be implemented in the child class to schedule the CRON task.
	 *
	 * @since 1.0.0
	 */
	abstract protected function schedule();

	/**
	 * Callback function to be executed when the CRON task runs.
	 *
	 * This method should be implemented in the child class to handle the CRON task logic.
	 *
	 * @since 1.0.0
	 */
	abstract protected function run();

	/**
	 * Hook the scheduled event and register the callback.
	 *
	 * This method adds the CRON task callback to the scheduled event.
	 *
	 * @since 1.0.0
	 */
	public function hook() {

		add_action( $this->get_hook_name(), array( $this, 'run' ) );
	}

	/**
	 * Unhook the scheduled event and remove the callback.
	 *
	 * This method removes the CRON task callback from the scheduled event.
	 *
	 * @since 1.0.0
	 */
	public function unhook() {

		remove_action( $this->get_hook_name(), array( $this, 'run' ) );
	}

	/**
	 * Get the unique hook name for the CRON task.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_hook_name() {

		return 'woo_paddle_gateway_' . $this->get_task_name();
	}

	/**
	 * Get the task name for the CRON task.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_task_name() {

		return $this->task_name;
	}
}
