<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 10:34
 */

namespace wujiangweiphp\yieldcsv;

use PDO;
use Exception;

class YieldCsv
{
    /**
     * @todo: 根据csv文件逐行读取数据
     * @author： friker
     * @date: 2019/1/8
     * @param string $file_name
     * @return Generator
     * @throws Exception
     */
    public function getCsvByFile($file_name = '')
    {
        if (empty($file_name) || !file_exists($file_name)) {
            throw new Exception('文件不存在');
        }
        $handle = fopen($file_name, 'r');
        while ($row = fgetcsv($handle, 1024)) {
            yield $row;
        }
        fclose($handle);
    }

    /**
     * @todo: 根据数据库逐行查询数据
     * @author： friker
     * @date: 2019/1/8
     * @param string $query 查询sql
     * @param array $connect_config 连接信息
     * @return Generator
     * @throws Exception
     */
    public function getCsvByMysql($query = '', $connect_config = array())
    {
        $host     = empty($connect_config['host']) ? '' : $connect_config['host'];
        $dbname   = empty($connect_config['dbname']) ? '' : $connect_config['dbname'];
        $username = empty($connect_config['username']) ? '' : $connect_config['username'];
        $password = empty($connect_config['password']) ? '' : $connect_config['password'];
        if (empty($host) || empty($dbname) || empty($username)) {
            throw new Exception('缺少数据库连接信息');
        }
        $mysql = $dbh = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password, [
            PDO::ATTR_PERSISTENT => true
        ]);
        $stmt  = $mysql->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    /**
     * @todo: 查询数据
     * @author： friker
     * @date: 2019/1/8
     * @param array $query_info
     * @param int $data_type
     * @return Generator
     * @uses       $query_info = array(
     * 'file_name'      => '',
     * 'fields'         => array(),
     * 'queyr'          => '',
     * 'connect_config' => ''
     * );
     * @throws Exception
     */
    public function getFileData($query_info = array(), $data_type = 1)
    {
        try {
            switch ($data_type) {
                case 1:
                    if (empty($query_info['file_name'])) {
                        throw new Exception('缺少文件信息');
                    }
                    $data = $this->getCsvByFile($query_info['file_name']);
                    break;
                case 2:
                    if (empty($query_info['query'])) {
                        throw new Exception('缺少查询信息');
                    }
                    if (empty($query_info['connect_config'])) {
                        throw new Exception('缺少连接信息');
                    }
                    $data = $this->getCsvByMysql($query_info['query'], $query_info['connect_config']);
                    break;
                default:
                    throw new Exception('数据类型错误');
                    break;
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        if (empty($query_info['fields'])) {
            throw new Exception('缺少查询字段');
        }
        $fields = $query_info['fields'];
        foreach ($data as $key => $val) {
            $temp = array();
            foreach ($fields as $k => $v) {
                $temp[] = $val[$k];
            }
            yield $temp;
        }
    }

    /**
     * @todo: 导出csv文件
     * @author： friker
     * @date: 2019/1/8
     * @param array $query_info
     * @param int $data_type
     * @throws Exception
     */
    public function exportCsv($query_info = array(), $data_type = 1)
    {
        $file_name = empty($query_info['file_name']) ? date('Y-m-d H:i:s') : $query_info['file_name'];
        try {
            $data      = $this->getFileData($query_info, $data_type);
            $file_name = iconv('utf-8', 'gbk', $file_name);
            $output = fopen('php://output', 'w') or die("can‘t open php://output");
            //告诉浏览器这个是一个csv文件
            header("Content-Type: application/csv");
            header("Content-Disposition: attachment; filename=$file_name.csv");
            header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
            header('Expires:0');
            header('Pragma:public');
            $head = array_values($query_info['fields']);
            foreach ($head as $i => $v) {
                $head[$i] = iconv('utf-8', 'gbk', $v);
            }
            fputcsv($output, $head);
            foreach ($data as $k => $v) {
                fputcsv($output, array_values($v));
            }
            fclose($output) or die("can‘t close php://output");
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }
}
