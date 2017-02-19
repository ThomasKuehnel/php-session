<?php

namespace TKuehnel\PhpSession;

use TKuehnel\PhpSession\Client\ClientInterface;

class SessionStreamWrapper
{

	const NAME = 'phpSession';
	const CONTEXT_OPTION_CLIENT = 'client';

	/**
	 * @var resource
	 */
	public $context;

	/**
	 * @var ClientInterface
	 */
	private $client;

	/**
	 * @var string
	 */
	private $currentPath;

	public function __construct() {
		$options = stream_context_get_options($this->context);
		$this->client = $options[static::NAME][static::CONTEXT_OPTION_CLIENT];
	}

	public function __destruct() {

	}

	/**
	 * @param string $path
	 * @param string $mode
	 * @param int $options
	 * @param string $opened_path
	 *
	 * @return bool
	 */
	public function stream_open($path, $mode, $options, &$opened_path) {
		$this->client->open();
		$this->currentPath = $path;

		return true;
	}

	/**
	 * @param int $count
	 *
	 * @return string
	 */
	public function stream_read($count) {
		$data = $this->client->get($this->currentPath);

		return '';
	}

	/**
	 * @param string $data
	 *
	 * @return int
	 */
	public function stream_write($data) {
		$this->client->set($this->currentPath, $data);

		return 0;
	}

	/**
	 * @return void
	 */
	public function stream_close() {
		$this->client->close();
	}

	/**
	 * @param int $operation
	 *
	 * @return bool
	 */
	public function stream_lock($operation) {
		var_dump("stream_lock");

		switch ($operation) {
			case LOCK_UN:
				$this->client->unlock($this->currentPath);

				return true;
			case LOCK_SH:
				$this->client->lockShared($this->currentPath);

				return true;
			case LOCK_EX:
				$this->client->lockExclusive($this->currentPath);

				return true;
		}
	}

}