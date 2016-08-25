<?php
/**
 * 基于文件实现的锁.
 * @author Leo
 *
 */
class FileLocker {
	/**
	 * 锁使用的文件句柄.
	 *
	 * @var resource
	 */
	private $lock;
	private $file;
	/**
	 * 创建锁，并同时尝试锁住它.
	 *
	 * @param string $name
	 *        	锁名.
	 * @param string $type
	 *        	锁类型.
	 */
	public function __construct($name = '0', $type = LOCK_EX) {
		$lockfile = TMP_PATH . '.' . $name;
		$this->file = $lockfile;
		if (! is_file ( $lockfile )) {
			if (! @touch ( $lockfile ) && ! is_file ( $lockfile )) {
				trigger_error ( 'cannot create lock file: ' . $lockfile, E_USER_WARNING );
			}
		}
		if (! is_file ( $lockfile )) {
			trigger_error ( 'cannot create lock file: ' . $lockfile, E_USER_WARNING );
		}
		$this->lock = @fopen ( $lockfile, 'r+' );
		if (! $this->lock) {
			trigger_error ( 'cannot create lock file: ' . $lockfile, E_USER_WARNING );
		}
		while ( ! @flock ( $this->lock, $type | LOCK_NB ) ) {
			usleep ( rand ( 1, 10 ) * 10 ); // 0-100 miliseconds
		}
	}
	/**
	 * 释放锁.
	 */
	public function unlock() {
		if ($this->lock) {
			@fclose ( $this->lock );
			// 尝试删除，如果没有其它用户在请求锁可正常删除
			@unlink ( $this->file );
		}
	}
}