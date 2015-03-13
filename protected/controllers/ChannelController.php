<?php
/**
 * @project hitour.server
 * @file ChannelController.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 14-7-15 下午5:25
 **/

class ChannelController extends Controller
{
    private $backup = 1;
    public $channel = '';

    public function filters()
    {
        return array(
            array(
                'application.filters.IpFilter + Export, Version, Search',
            ),
        );
    }

    public function init()
    {
        $this->channel = isset($_REQUEST['channel']) ? $_REQUEST['channel'] : '';
        $this->backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : 1;
        return true;
    }

    public function actionExport()
    {
        $class = ucfirst(strtolower($this->channel));
        if (!empty($class) && class_exists($class)) {
            $xml = (new $class())->export();
            if (empty($xml)) {
                Yii::log('Export for channel['.$this->channel.'], xml data is empty.', CLogger::LEVEL_WARNING);
            }else {
                $this->outputXML($xml);
            }
        }else{
            Yii::log('Export for channel, but channel['.$this->channel.'] is not supported.', CLogger::LEVEL_WARNING);
        }
    }

    private function outputXML($xml)
    {
        $data = '<?xml version="1.0" encoding="utf-8" ?>';
        if (strtolower($this->channel) == 'sougou') {
            $data .= '<sdd>'.$xml.'</sdd>';
        }else{
            $data .= '<document>'.$xml.'</document>';
        }
        if ($this->backup) {
            $datestr = date('Ymd', time());
            $path = dirname(Yii::app()->basePath) . '/data/channel/' . $this->channel;
            if(!file_exists($path)){
                mkdir($path,0755,true);
            }
            file_put_contents($path . '/hitour_product_'. $datestr .'.xml', $data);
            $this->specialOutput($path, $data);
        }
        header('Content-Type: text/xml');
        echo $data;
    }

    private function specialOutput($path, $data)
    {
        if (strtolower($this->channel) == 'qh360') {
            $output = 'http://www.hitour.cc/data/channel/qh360/new_1.xml';
            file_put_contents($path . '/new_index.txt', $output);
            file_put_contents($path . '/new_1.xml', $data);
        }
        if (strtolower($this->channel) == 'sougou') {
            $date_str = date('Ymd');
            $output = '<?xml version="1.0" encoding="utf-8" ?>';
            $output .= '<sddindex>';
            //对XML数据进行切分
            $unit_size = 2 * 1024 * 1024;
            $total_size = strlen($data);
            $file_num = 1 + (int)(strlen($data) / $unit_size);
            $pos1 = $pos2 = 0;
            for($i = 1; $i <= $file_num; $i++) {
                $xml = '<?xml version="1.0" encoding="utf-8" ?>';
                $xml .= '<sdd><datalist>';
                $pos1 = stripos($data, '<item><pname>', $pos2);
                $mpos = $pos1 + ($i * $unit_size);
                $pos2 = stripos($data, '</rank></item>', $mpos >= $total_size ? $total_size : $mpos);
                if (false === $pos2) {
                    $xml .= substr($data, $pos1);
                }else{
                    $xml .= substr($data, $pos1, $pos2 - $pos1 + 14);
                    $xml .= '</datalist></sdd>';
                }
                file_put_contents($path . '/hitour_'.$date_str.'_'.$i, $xml);

                $output .= '<sdd>';
                $output .= '<loc>http://www.hitour.cc/data/channel/sougou/hitour_'.$date_str.'_'.$i.'</loc>';
                $output .= '<MD5>'.(md5($xml)).'</MD5>';
                $output .= '<lastmod>'.(date('Y-m-d')).'</lastmod>';
                $output .= '</sdd>';
                if (false === $pos2) {
                    break;
                }
            }
            $output .= '</sddindex>';
            file_put_contents($path . '/map.xml', $output);
        }
    }

    public function actionVersion()
    {
        echo date('Ymd');
    }

    public function actionCps()
    {
        //1.CPS联盟推广进入获取跳转link
        $target_url = Yii::app()->cps->markUnion();
        if (empty($target_url)) {
            $target_url = $this->createAbsoluteUrl('');
        }
        $this->redirect($target_url);
    }

    public function actionCpc()
    {
        //1.CPC联盟推广进入获取跳转link
        $target_url = Yii::app()->cps->markUnion(0);
        if (empty($target_url)) {
            $target_url = $this->createAbsoluteUrl('');
        }
        $this->redirect($target_url);
    }

    public function actionSearch()
    {
        $data = Yii::app()->cps->search();
        if (!empty($data) && !empty($data['data']) && is_array($data['data'])) {
            if ($data['code'] == 200) {
                EchoUtility::echoCommonMsg(200, $data['msg'], $data['data']);
            }else{
                EchoUtility::echoCommonMsg(400, $data['msg']);
            }
        }else if (!is_array($data['data'])) {
            echo $data['data'];
        }
    }
}
