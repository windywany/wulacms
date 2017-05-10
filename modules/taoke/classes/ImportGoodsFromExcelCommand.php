<?php
declare(ticks = 5);

namespace taoke\classes;

use artisan\ArtisanCommand;
use cms\classes\ChannelImporter;
use cms\classes\ChannelImporterParam;
use taoke\command\DeleteExpireGoodsCommand;
use taoke\command\ImportTask;
use taoke\command\RebuildXSIndexCommand;
use taoke\command\UpdateSearchIndexCommand;

class ImportGoodsFromExcelCommand extends ArtisanCommand {
	public function cmd() {
		return 'tbk_import';
	}

	public function desc() {
		return 'import taobao goods';
	}

	protected function execute($options) {
		if (isset($options['file'])) {
			$file = $options['file'];
		} else {
			$file = $this->opt(-1, 'tbk.xlsx');
		}

		if (!is_file($file)) {
			$this->log('the file ' . $file . ' dose not exist!');
			exit(1);
		}

		$this->log('start importing form: ' . $file);

		$phpexcel = \PHPExcel_IOFactory::load($file);

		$phpexcel->setActiveSheetIndex(0);

		$sheet = $phpexcel->getActiveSheet();

		$parma                      = new ChannelImporterParam();
		$parma->default_model       = 'taoke';
		$parma->default_template    = 'taoke.tpl';
		$parma->default_url_pattern = 'tbk/{aid}.html';
		$parma->page_name           = 'index.html';
		$parma->index_page_tpl      = 'taobaoke/category.tpl';
		$parma->list_page_tpl       = 'taobaoke/list.tpl';
		$parma->list_page_name      = '{path}/list.html';
		$parma->is_topic_channel    = 0;
		$importer                   = new ChannelImporter($parma);

		$defs = ['title' => 'B', 'image' => 'C', 'goods_id' => 'A', 'channel' => 'E', 'goods_url' => 'D', 'tbk_url' => 'F', 'price' => 'G', 'sale_count' => 'H', 'rate' => 'I', 'comission' => 'J', 'wangwang' => 'K', 'wangwangid' => 'L', 'shopname' => 'M', 'platform' => 'N', 'coupon_count' => 'P', 'coupon_remain' => 'Q', 'coupon_price' => 'R', 'coupon_start' => 'S', 'coupon_stop' => 'T', 'coupon_url' => 'V'];

		$request = \Request::getInstance();
		$i       = 2;
		$date    = date('Y-m-d');
		while (true) {
			$data = $this->getData($defs, $sheet, $i);
			if (empty($data['goods_id'])) {
				break;
			}
			if ($data['coupon_stop'] < $date) {
				continue;
			}
			$channel = $importer->importByNames($data['channel']);
			if ($channel) {
				$data['channel'] = $channel;
			}
			$data['model']     = 'taoke';
			$data['type']      = 'page';
			$coupon_price      = $data['coupon_price'];
			$data['use_price'] = 0;
			if (preg_match('#.+?(\d+).+?(\d+)#', $coupon_price, $ms)) {
				$data['use_price'] = $ms[1];
				$data['discount']  = $ms[2];
			} elseif (preg_match('#.*?(\d+).+#', $coupon_price, $ms)) {
				$data['discount'] = $ms[1];
			} else {
				$data['discount'] = 0;
			}
			if (floatval($data['use_price']) <= floatval($data['price'])) {
				$data['real_price'] = number_format(floatval($data['price']) - floatval($data['discount']), 2);
			} else {
				$data['real_price'] = $data['price'];
			}
			$request->addUserData($data, true);
			$id = \CmsPage::save('page', 'taoke', null, false);
			if ($id) {
				$this->log('imported - ' . $id);
			} else {
				$this->log('cannot import :' . $data['goods_id']);
			}
			$i++;
		}
		rename($file, $file . '.bak');

		return 0;
	}

	protected function getLongOpts() {
		return ['file::excel file' => 'the excel file contains goods information.'];
	}

	private function getData($def, \PHPExcel_Worksheet $sheet, $i) {
		$data = [];
		foreach ($def as $d => $c) {
			$data[ $d ] = $sheet->getCell($c . $i)->getValue();
		}

		return $data;
	}

	public static function get_artisan_commands($commands) {
		$commands['tbk_import']       = new self();
		$commands['tbk_import1']      = new ImportTask();
		$commands['del-goods']        = new DeleteExpireGoodsCommand();
		$commands['update-searchidx'] = new UpdateSearchIndexCommand();
		$commands['rebuild-taokeidx'] = new RebuildXSIndexCommand();

		return $commands;
	}
}