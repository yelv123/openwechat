<?php
/**
 * Created by PhpStorm.
 * User: wanda
 * Date: 2018/4/26
 * Time: 下午5:50
 */

namespace WechatLogin;

use function PHPSTORM_META\elementType;
use Requests;

class WechatLogin
{
    private $appId;
    private $appSecret;
    private $Requests;
    public $error = '';
    public $errorNo = '';

    public function __construct($appId = "", $appSecret = "")
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->Requests  = new \Requests_Session("https://api.weixin.qq.com/");
    }


    /**
     * getAccessToken
     */
    private function getAccessToken($code)
    {
        try {
            $url                 = "/sns/oauth2/access_token?";
            $param['appid']      = $this->appId;
            $param['secret']     = $this->appSecret;
            $param['code']       = $code;
            $param['grant_type'] = "authorization_code";
            $url                 = $url . http_build_query($param);
            $getAccessToken      = $this->Requests->get($url);
            $getAccessToken      = json_decode($getAccessToken->body, true);
            if (isset($getAccessToken['errcode']))
            {
                $this->error=$getAccessToken['errcode'];
                $this->errorNo=$getAccessToken['errmsg'];
                return false;
            }
            else
            {

                $data['access_token']=$getAccessToken['access_token'];
                $data['openid']=$getAccessToken['openid'];
                return $data;
            }
        }
        catch (\Exception $e)
        {
            $this->error="与微信服务器通信异常";
            $this->error="Communication with WeChat server is abnormal.";
            return false;

        }
    }

    /**
     * 获取用户的信息
     */
    public function getUserInfo($code)
    {
        try{
            $accessToken=$this->getAccessToken($code);
            if($accessToken)
            {
               $url="/sns/userinfo?".http_build_query($accessToken);
               $userInfo     = $this->Requests->get($url);
               $userInfo      = json_decode($userInfo->body, true);
                if (isset($userInfo['errcode']))
                {
                    $this->error=$userInfo['errcode'];
                    $this->errorNo=$userInfo['errmsg'];
                    return false;
                }
                else
                {
                    return $userInfo;
                }
            }
            else
                {
                return false;
            }

        }
        catch (\Exception $e)
        {
            $this->error="与微信服务器通信异常";
            $this->error="Communication with WeChat server is abnormal.";
            return false;
        }

    }
}