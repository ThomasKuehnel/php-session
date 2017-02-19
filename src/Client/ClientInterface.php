<?php

namespace TKuehnel\PhpSession\Client;

interface ClientInterface
{

	public function open();

	public function close();

	public function get(string $key);

	public function set(string $key, string $data);

	public function lockShared(string $key);

	public function lockExclusive(string $key);

	public function unlock(string $key);
}