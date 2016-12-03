<?php
namespace artisan;

abstract class ArtisanDaemonTask extends ArtisanCommand {
	private $maxParallel = 1;
	private $isParent    = true;
	private $shutdown    = false;
	const WORK_DONE_EXIT_CODE = 42;

	public final function run() {
		if (!function_exists('pcntl_fork')) {
			$this->log('miss pcntl');
			exit(1);
		}
		$cmd     = $this->cmd();
		$options = $this->getOptions();
		$pid     = pcntl_fork();
		if ($pid > 0) {
			exit(0);
		} elseif (0 === $pid) {
			umask(0);
			openlog('daemon-' . $cmd, LOG_PID | LOG_PERROR, LOG_LOCAL0);
			$sid = posix_setsid();
			if ($sid < 0) {
				syslog(LOG_ERR, 'Could not detach session id.');
				exit(1);
			}
			$this->setUp($options);
			$this->doStartLoop($options);
			$this->tearDown($options);
			closelog();
			exit(0);
		}

		return 0;
	}

	private function doStartLoop($options) {
		$this->maxParallel = isset($options['workerCount']) ? $options['workerCount'] : 1;
		$parallel          = $this->maxParallel;
		$forks             = array();
		$this->initSignal($forks);
		$i = 0;
		while (count($forks) < $parallel) {
			$pid = pcntl_fork();
			if (0 === $pid) {
				$this->isParent = false;
				$this->initSignal();
				$ops      = array_merge(['taskId' => $i], $options);
				$exitCode = $this->execute($ops);
				usleep(1000000);
				exit($exitCode === true ? self::WORK_DONE_EXIT_CODE : 0);
			} else {
				$forks[ $pid ] = $pid;
			}
		}

		do {
			// Check if the registered jobs are still alive
			if ($pid = pcntl_wait($status)) {
				if (self::WORK_DONE_EXIT_CODE === pcntl_wexitstatus($status)) {
					$parallel = $this->maxParallel;
				} else if ($parallel > 1) {
					$parallel = $parallel - 1;
				}
				unset($forks[ $pid ]);
			}
		} while (count($forks) >= $parallel);
	}

	// 准备任务
	protected function setUp(&$options) {

	}

	// 运行完成处理
	protected function tearDown(&$options) {

	}

	private function initSignal(&$workers = null) {
		$signals = array(SIGTERM, SIGINT);
		foreach (array_unique($signals) as $signal) {
			pcntl_signal($signal, function ($signal) use ($workers) {
				if ($this->isParent) {
					$this->log("Shutdown Signal Received\n");
					if ($workers) {
						foreach ($workers as $pid) {
							@posix_kill($pid, $signal);
						}
					}
				} else {
					$this->shutdown = true;
				}
			});
		}
	}

	/**
	 * 更新进度
	 *
	 * @param $a
	 * @param $b
	 */
	protected function update($a, $b) {

	}
}