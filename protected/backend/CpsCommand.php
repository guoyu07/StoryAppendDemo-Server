<?php
/**
 * Created by PhpStorm.
 * User: app
 * Date: 14-5-24
 * Time: 下午6:45
 */
/**
 * @project hitour.server
 * @file CpsCommand.php
 * @author xudong(zxd@hitour.cc)
 * @version 1.0
 * @date 15-2-27 下午6:45
**/

class CpsCommand extends CConsoleCommand {

    public $cps_log_table;
    private $server_ips = array(
        '10.0.0.1','10.0.0.2','10.0.0.3','10.0.0.4','10.0.0.5','10.0.0.6','10.0.0.7',
        '113.31.82.134','113.31.82.135','113.31.82.136','113.31.82.137'
    );

    public function usageError($message)
    {
        echo("Error: $message\n\n".$this->getHelp()."\n");
        exit(1);
    }

    public function runError($message)
    {
        echo("Error: $message\n\n");
        exit(1);
    }

    public function init()
    {
        $this->cps_log_table = Yii::app()->mongodb->getDbInstance()->selectCollection('cps_log');
        return true;
    }

    public function actionIndex()
    {
        echo 'Usage: '."\n\n";
        echo '    php entry.php cps import' . "\n";
        echo "\n";
    }

    public function actionImport($from = '', $to = '')
    {
        if (!empty($from) && !empty($to)) {
            $from = date('Ymd', strtotime($from));
            $to = date('Ymd', strtotime($to));
            for($i = $from; $i <= $to; $i++) {
                $logfile = Yii::app()->params['WEB_LOG_PATH'].'access.log-'.$i;
                if (file_exists($logfile)) {
                    $this->importFromLogFile($logfile);
                }
            }
        }else{
            $from = date('Ymd', time() - 86400);
            $logfile = Yii::app()->params['WEB_LOG_PATH'].'access.log-'.$from;
            if (file_exists($logfile)) {
                $this->importFromLogFile($logfile);
            }else{
                echo 'Not found log file:['.$logfile.']'."\n\n";
            }
        }
    }

    private function importFromLogFile($logfile)
    {
        $fp = fopen($logfile, 'r');
        if ($fp) {
            while(!feof($fp)) {
                $buf = fgets($fp, 8192);
                $pos = stripos($buf, 'spider');
                if ($pos) {
                    continue;
                }

                $vars     = explode('"', $buf);
                $normals  = explode(' ', $vars[0]);
                $urls     = explode(' ', $vars[1]);
                $codes    = explode(' ', $vars[2]);
                $refers   = explode(' ', $vars[3]);
                $time_str = trim($normals[5], '[') . ' ' . trim($normals[6], ']');

                $data = array();
                $data['ip']      = trim($normals[0]);
                $data['request_time'] = trim($normals[3]);
                $data['response_time'] = trim($normals[4]);
                $data['time']    = date('Y-m-d H:i:s',strtotime(trim($time_str)));
                $data['method']  = trim($urls[0]);
                $data['url']     = trim($urls[1]);
                $data['code']    = trim($codes[1]);
                $data['size']    = trim($codes[2]);
                $data['refer']   = trim($refers[0]);
                $data['ua']      = trim($vars[5]);

                if (in_array($data['ip'], $this->server_ips)) {
                    continue;
                }

                $urls = parse_url($data['url']);
                $query = $urls['query'];
                if (strcasecmp($urls['path'],'/channel/cps') != 0 && strcasecmp($urls['path'],'/channel/cpc') != 0) {
                    continue;
                }
                parse_str($query, $params);
                if (empty($params) || !is_array($params)) {
                    continue;
                }
                $data['union']    = $params['union'];
                $data['source']   = $params['source'];
                $data['cid']      = $params['cid'];
                $data['jump_url'] = $params['url'];

                $this->cps_log_table->insert($data);
            }
            fclose($fp);
        }
    }

}