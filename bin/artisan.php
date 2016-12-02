<?php
namespace {

	/*
	 * the entry of cron
	 */
	use artisan\ArtisanHelpCommand;

	define('WEB_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
	include_once WEB_ROOT . 'bootstrap.php';
	@ob_end_clean();
	// comand list
	$commands = apply_filter('get_artisan_commands', ['help' => new ArtisanHelpCommand()]);
	set_time_limit(0);
	$cmd = isset($argv[1]) ? $argv[1] : 'help';

	if (!isset($commands[ $cmd ])) {
		$commands['help']->help("Unkown Command: " . $cmd);
	} else {
		exit($commands[ $cmd ]->run());
	}
}

namespace artisan {
	abstract class ArtisanCommand {
		public function help($message = '') {
			if ($message) {
				echo "ERROR:\n";
				echo "  " . wordwrap($message, 72, "\n  ") . "\n\n";
			}
			$opts = $this->getOpts();
			echo wordwrap($this->desc(), 72, "\n  ") . "\n\n";
			echo "USAGE:\n";
			echo "  # " . basename(__FILE__) . ' ' . $this->cmd() . ' [options] ' . "\n";
			foreach ($opts as $opt => $msg) {
				$opss = explode(':', $opt);
				$l    = count($opss);
				$arg  = $opss[ $l - 1 ];
				$str  = str_pad($opss[0] . ($arg && $l == 2 ? " <$arg>" : ($arg && $l == 3 ? " [$arg]" : '')), 24, ' ', STR_PAD_RIGHT);
				echo wordwrap("    -" . $str . $msg, 72, "\n ") . "\n";
			}
			$opts = $this->getLongOpts();
			foreach ($opts as $opt => $msg) {
				$opss = explode(':', $opt);
				$l    = count($opss);
				$arg  = $opss[ $l - 1 ];
				$str  = str_pad($opss[0] . ($arg && $l == 2 ? " <$arg>" : ($arg && $l == 3 ? " [$arg]" : '')), 24, ' ', STR_PAD_RIGHT);
				echo wordwrap("    --" . $str . $msg, 72, "\n ") . "\n";
			}
			exit(1);
		}

		protected function getOpts() {
			return [];
		}

		protected function getLongOpts() {
			return [];
		}

		protected function getOptions() {
			global $argv, $argc;
			$op   = [];
			$opts = $this->getOpts();
			foreach ($opts as $opt => $msg) {
				$opss                 = explode(':', $opt);
				$l                    = count($opss);
				$op[ '-' . $opss[0] ] = $l;
			}
			$opts = $this->getLongOpts();
			foreach ($opts as $opt => $msg) {
				$opss                  = explode(':', $opt);
				$l                     = count($opss);
				$op[ '--' . $opss[0] ] = $l;
			}
			$options = [];
			foreach ($op as $o => $r) {
				$key = trim($o, '-');
				for ($i = 2; $i < $argc; $i++) {
					if ($argv[ $i ] == $o) {
						if ($r == 1) {
							$options[ $key ] = true;
							break;
						}
						for ($j = $i + 1; $j < $argc; $j++) {
							$v = $argv[ $j ];
							if ($v == '=') {
								continue;
							} elseif (strpos('-', $v) === 0) {
								break;
							} else {
								$options[ $key ] = $v;
								break;
							}
						}
					}
				}
				if ($r == 2 && !isset($options[ $key ])) {
					$this->help('Miss option:' . $o);
				}
			}

			return $options;
		}

		protected function log($message) {
			echo $message, "\n";
			flush();
		}

		public final function run() {
			$options = $this->getOptions();

			return $this->execute($options);
		}

		public abstract function cmd();

		public abstract function desc();

		protected abstract function execute($options);
	}

	class ArtisanHelpCommand extends ArtisanCommand {
		public function help($message = '') {
			global $commands;
			if ($message) {
				echo "ERROR:\n";
				echo "  " . wordwrap($message, 72, "\n  ") . "\n\n";
			} else {
				echo "artisan manager script\n\n";
				echo "USAGE:\n";
			}
			echo "  # " . basename(__FILE__) . " <command> [options]\n";
			echo "   command list:\n";

			foreach ($commands as $name => $cmd) {
				echo wordwrap("     " . $name . "\t\t" . $cmd->desc(), 72, "\n  ") . "\n";
			}
			echo "\n  # " . basename(__FILE__) . ' help <command> to list command options' . "\n";
			exit(1);
		}

		protected function execute($options) {
			global $argv, $commands;

			if (isset($argv[2]) && isset($commands[ $argv[2] ])) {
				$commands[ $argv[2] ]->help();
			} else {
				$this->help();
			}
		}

		public function desc() {
			return 'print this text';
		}

		public function cmd() {
			return 'help';
		}
	}
}

