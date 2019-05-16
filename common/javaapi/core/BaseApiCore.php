<?php
/**
 * User: Ron
 * Date: 2017/11/01 下午4:11
 * java api core 中的父类
 */

namespace common\javaapi\core;

class BaseApiCore {

    public $_params = array();
    public $_headersJson = array('Content-type: application/json;charset=utf-8');
    public $_headerRwa = array('Content-Type: text/raw');
    public $_poststring = 'POSTSTRING';
    public $_thePostBuild = 'POSTBUILD';
    public $_thePost = 'POST';
    public $_theGet = 'GET';

    /**
     * 给参数批量赋值
     *
     * @param array $columnArr 需要赋值的字段列表
     * @param array $dataArr 数据
     */
    protected function _parameterAssignment($columnArr, $dataArr)
    {
        if (!empty($columnArr) && !empty($dataArr) && is_array($columnArr) && is_array($dataArr)) {
            foreach ($dataArr as $key => $val) {
                if (in_array($key, $columnArr)) $this->_params[$key] = $dataArr[$key];
            }
        }
    }

    /**
     * 统一返回的错误信息内容
     *
     * @param array $returnWithData 需要合并到返回中的格式化内容
     * @return array
     */
    protected function _returnData($returnWithData = [])
    {
        $resContent = [
            'result' => 1,//status: 0:成功，1：失败
            'message' => '',//提示信息
            'messageType' => 0,//消息类型(0:正常,1系统异常,2常规异常)
            'messageCode' => '0',//消息代码 (3-1000之间是购物车服务错误码)
        ];
        if (!empty($returnWithData)) $resContent = array_merge($resContent, $returnWithData);
        return $resContent;
    }

    /**
     * 统一返回的错误信息内容
     *
     * @param array $returnWithData 需要合并到返回中的格式化内容
     * @return array
     */
    protected function _returnDataForOrder($returnWithData = [])
    {
        $resContent = [
            'status' => false,//status: false:失败，true：成功
            'message' => '',//提示信息
            'messageCn' => '',//消息类型
            'errorCode' => '1',//错误代码 1：系统错误……
        ];
        if (!empty($returnWithData)) $resContent = array_merge($resContent, $returnWithData);
        return $resContent;
    }

}