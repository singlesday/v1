<?php

namespace common\modules\article\actions;

use yii\base\Action;
use yii\web\UploadedFile;
use yii\imagine\Image;
use Imagine\Image\Box;
use Yii;
use yii\web\Response;
use finfo;

/**
 * Képfeltöltés 
 *
 * @author makszipeter
 */
class UploadAction extends Action {

    const THUMBNAILPREFIX = 'thumb_';
    private $fullSize = 1024;
    private $thumbnailSize = 215;
    
    
    public function run() {
        $image = UploadedFile::getInstanceByName('Filedata');
        $imageTempLocation = $image->tempName;
        $finfo = new finfo(FILEINFO_MIME);
        $finfoMime = $finfo->file($image->tempName);
        $isImage = explode('/', $finfoMime);
        if ($isImage[0] !== 'image') {
            return -1;
        }
        $imageModel = new \common\modules\article\models\Image();
        $imageModel->status = $imageModel::ACTIVE;
        $imageModel->extension = $image->extension;
        $imageModel->save();

        //finfo_file($finfo, $filename, $options, $context)
        $imageDirLocation = \Yii::getAlias('@webroot') . '/images/';
        Image::getImagine()->open($imageTempLocation)->thumbnail(new Box($this->fullSize, $this->fullSize))->
                save($imageDirLocation . $imageModel->id . '.' . $image->extension, ['quality' => 90]);
        Image::thumbnail($imageTempLocation, $this->thumbnailSize, $this->thumbnailSize)
                ->save($imageDirLocation . self::THUMBNAILPREFIX . $imageModel->id . '.' . $image->extension, ['quality' => 80]);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['id' => $imageModel->id, 'ext' => $imageModel->extension];
        return $response;
    }

}
