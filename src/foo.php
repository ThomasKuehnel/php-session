<?php

require_once 'SessionStreamWrapper.php';
require_once 'Client/ClientInterface.php';
require_once 'Client/Redis/RedisClient.php';

register_shutdown_function(function() {
	echo "shutdown" . PHP_EOL;
});

$redisClient = new \TKuehnel\PhpSession\Client\Redis\RedisClient();

stream_context_set_default([
	\TKuehnel\PhpSession\SessionStreamWrapper::NAME => [
		\TKuehnel\PhpSession\SessionStreamWrapper::CONTEXT_OPTION_CLIENT => $redisClient,
	],
]);

stream_register_wrapper(\TKuehnel\PhpSession\SessionStreamWrapper::NAME, '\TKuehnel\PhpSession\SessionStreamWrapper');

$sessionLock = new SessionLock();
$sessionLock->lock();

sleep(60);


class SessionLock {
	protected $fileHandle;

	public function __destruct()
	{
		$this->unlock();
	}

	public function lock()
	{
		$this->fileHandle = fopen('phpSession://test', 'w');
		flock($this->fileHandle, LOCK_EX);
	}

	public function unlock()
	{
		if (!$this->fileHandle) {
			return;
		}

		flock($this->fileHandle, LOCK_UN);
		fclose($this->fileHandle);

		$this->fileHandle = null;
	}
}