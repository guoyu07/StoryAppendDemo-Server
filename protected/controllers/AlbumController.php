<?php

/**
 * Created by PhpStorm.
 * User: HiTour team
 * Date: 4/14/14
 * Time: 12:18 PM
 */
class AlbumController extends Controller
{
    /*
     * 获取专辑基本信息
     */
    public function actionGetAlbumInfo()
    {
        $album = Album::model()->findByPk($this->getAlbumID());

        echo CJSON::encode(array('code' => 200, 'data' => $album));
    }

    /*
     * 获取专辑包含的地点信息
     */
    public function actionGetAlbumLandInfos()
    {
        $album_id = $this->getAlbumID();

        $data = Landinfo::model()->getLandinfos($album_id);

        echo CJSON::encode(array('code' => 200, 'data' => $data));
    }

    private function getAlbumID()
    {
        return (int)Yii::app()->request->getParam('album_id');
    }


}