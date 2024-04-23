<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

declare(strict_types=1);

namespace app\api;

use think\App;
use think\exception\HttpResponseException;
use think\facade\Request;
use think\facade\Session;
use think\facade\View;
use think\facade\Db;
use think\Response;
/**
 * Use the fully-qualified AllowDynamicProperties, otherwise the #[AllowDynamicProperties] attribute on "BaseController" WILL NOT WORK.
 */
use \AllowDynamicProperties;

/**
 * コントローラの基本クラス
 */
#[AllowDynamicProperties]
abstract class BaseController
{
    /**
     * Requestインスタンス
     * @var \think\Request
     */
    protected $request;

    /**
     * アプリケーションインスタンス
     * @var \think\App
     */
    protected $app;

    /**
     * バッチ検証フラグ
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * コントローラミドルウェア
     * @var array
     */
    protected $middleware = [];

    /**
     * ページサイズ
     * @var string
     */
    protected $pageSize = '';

    /**
     * jJWT設定
     * @var string
     */
    protected $jwt_conf = [
        'secrect' => 'gouguoa',
        'iss' => 'www.gougucms.com',
        // 発行者 オプション
        'aud' => 'gouguoa',
        // このJWTを受け取る側 オプション
        'exptime' => 7200, // 有効期限、ここでは2時間に設定
    ];

    /**
     * コンストラクタ
     * @access public
     * @param  App $app アプリケーションオブジェクト
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->module = strtolower(app('http')->getName());
        $this->controller = strtolower($this->request->controller());
        $this->action = strtolower($this->request->action());
        $this->uid = 0;
        $this->did = 0;
        $this->annual_leave=0;
        $this->jwt_conf = get_system_config('token');
        // コントローラの初期化
        $this->initialize();
    }

    // 初期化
    protected function initialize()
    {
        // ログインのチェック
        $this->checkLogin();
        // 1ページ当たりのデータ数
        $this->pageSize = Request::param('page_size', \think\facade\Config::get('app.page_size'));
    }

    /**
     * ユーザーのログインを検証する
     */
    protected function checkLogin()
    {
        $session_admin = get_config('app.session_admin');
        if (!Session::has($session_admin)) {
            $this->apiError('ログインしてください');
        } else {
            $this->uid = Session::get($session_admin);
            $login_admin = Db::name('Admin')->where(['id' => $this->uid])->find();
            $this->did = $login_admin['did'];
            $this->annual_leave= $login_admin['annual_leave'];
            View::assign('login_admin', $login_admin);
        }
    }
    /**
     * API処理成功の結果を返すメソッド
     * @param      $message
     * @param null $redirect
     * @param null $extra
     * @return mixed
     * @throws ReturnException
     */
    protected function apiSuccess($msg = 'success', $data = [])
    {
        return $this->apiReturn($data, 0, $msg);
    }

    /**
     * API処理失敗の結果を返すメソッド
     * @param      $error_code
     * @param      $message
     * @param null $redirect
     * @param null $extra
     * @return mixed
     * @throws ReturnException
     */
    protected function apiError($msg = 'fail', $data = [], $code = 1)
    {
        return $this->apiReturn($data, $code, $msg);
    }

    /**
     * クライアントにAPIデータを返す
     * @param  mixed   $data 返すデータ
     * @param  integer $code 返すコード
     * @param  mixed   $msg メッセージ
     * @param  string  $type データの形式
     * @param  array   $header 送信するヘッダ情報
     * @return Response
     */
    protected function apiReturn($data, int $code = 0, $msg = '', string $type = '', array $header = []): Response
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $type = $type ?: 'json';
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

}