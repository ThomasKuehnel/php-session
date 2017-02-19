<?php

namespace TKuehnel\PhpSession\Client\Redis;

use TKuehnel\PhpSession\Client\ClientInterface;

class RedisClient implements ClientInterface
{

	const LOCK_SHARED = 'shared';
	const LOCK_EXCLUSIVE = 'exclusive';

	/**
	 * @var \Redis
	 */
	private $redis;

	public function __construct()
	{
		$this->redis = new \Redis();
	}

	public function open()
	{
		$this->redis->connect('redis');
	}

	public function close()
	{
		$this->redis->close();
	}

	/**
	 * @param string $key
	 * @return bool|string
	 */
	public function get(string $key)
	{
		return $this->redis->get($key);
	}

	/**
	 * @param string $key
	 * @param string $data
	 * @return bool
	 */
	public function set(string $key, string $data)
	{
		return $this->redis->set($key, $data);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function lockShared(string $key)
	{
		return $this->redis->set($this->generateLockKey($key), static::LOCK_SHARED);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function lockExclusive(string $key)
	{
		return $this->redis->set($this->generateLockKey($key), static::LOCK_EXCLUSIVE);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function unlock(string $key)
	{
		return ($this->redis->del($this->generateLockKey($key)) > 0);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	private function generateLockKey(string $key) {
		return sprintf('%s_lock', $key);
	}

}