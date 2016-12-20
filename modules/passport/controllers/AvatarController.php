<?php

namespace passport\controllers;

use media\classes\PluploadController;
use passport\models\MemberModel;

class AvatarController extends PluploadController {
	/**
	 * @param string $file 头像图片路径.
	 * @param int    $x
	 * @param int    $y
	 * @param int    $w
	 * @param int    $h
	 *
	 * @return \JsonView
	 */
	public function save_post($file, $x = 0, $y = 0, $w = 0, $h = 0) {
		$data['success'] = false;
		$user            = passport();
		if (!$user->isLogin()) {
			$data['msg'] = '请登录';

			return $data;
		}

		$avatarFile = WEB_ROOT . $file;
		if (is_file($avatarFile)) {
			$image   = new \ImageUtil($avatarFile);
			$bigFile = get_thumbnail_filename($avatarFile, 'big', 0, '_');
			$file    = $image->crop($x, $y, $w, $h, $bigFile);
			if ($file) {
				$image     = new \ImageUtil($file);
				$fs        = $image->thumbnail([[60, 60, 'small']], '_', '_big');
				$smallFile = null;
				$data      = [];
				if ($fs) {
					$smallFile = $fs[0];
				}
				$savePath = '@' . \MediaUploadHelper::getDestPath(cfg('avatar_dir@passort', 'avatar'));
				$uploader = \MediaUploadHelper::getUploader();
				$bg       = $uploader->save($bigFile, $savePath);
				if ($bg) {
					$data['avatar'] = $bg[0];
				}
				@unlink($avatarFile);
				@unlink($bigFile);
				if ($smallFile) {
					$sm = $uploader->save($smallFile, $savePath);
					@unlink($smallFile);
					if ($sm) {
						$data['avatar_small'] = $sm[0];
					}
				}

				if ($data) {
					$model = new MemberModel();
					$model->update($data, ['mid' => $user->getUid()]);
					$user['avatar']       = $data['avatar'];
					$user['avatar_small'] = $data['avatar_small'];
					$user->save();
					$data['success'] = true;
				} else {
					$data['msg'] = '无法处理文件';
				}
			}
		} else {
			$data['msg'] = '头像文件不存在';
		}

		return new \JsonView($data);
	}

	public function canUpload() {
		$user = passport();
		if ($user->isLogin()) {
			return true;
		} else {
			return false;
		}
	}
}