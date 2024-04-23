<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/Apache-2.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use app\api\middleware\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Db;
use think\facade\Request;

class Demo extends BaseController
{
    /**
     * コントローラーミドルウェア [ログイン、登録は認証が不要]
     * @var array
     */
	protected $middleware = [
    	Auth::class => ['except' 	=> ['index','login'] ]
    ];

    /**
     * @param $user_id
     * @return string
     */
    public function getToken($user_id){
        $time = time(); //現在時間
		$conf = $this->jwt_conf;
        $token = [
            'iss' => $conf['iss'], //署名者　選択可能
            'aud' => $conf['aud'], //JWTの受信者　選択可能
            'iat' => $time, //発行時間
            'nbf' => $time-1 , //(Not Before)：アクセス可能な時間
            'exp' => $time+$conf['exptime'], //有効期限、ここでは2時間を設定
            'data' => [
                //カスタム情報、機密情報は設定しないでください
                'userid' =>$user_id,
            ]
        ];
        return JWT::encode($token, $conf['secrect'], 'HS256'); //Tokenの出力  デフォルト'HS256'
    }

    /**
     * @param $token
     */
    public static function checkToken($token){
        try {
            JWT::$leeway = 60;//現在時間から60を引く、時間を少し余裕を持つ
            $decoded = JWT::decode($token, self::$config['secrect'], ['HS256']); //HS256方式、これは署名時と対応しています
            return (array)$decoded;
        } catch(\Firebase\JWT\SignatureInvalidException $e) {  //署名が正しくありません
            return json(['code'=>403,'msg'=>'署名エラー']);
        }catch(\Firebase\JWT\BeforeValidException $e) {  //署名はある時間点から利用可能
            return json(['code'=>401,'msg'=>'token失効']);
        }catch(\Firebase\JWT\ExpiredException $e) {  //tokenが期限切れ
            return json(['code'=>401,'msg'=>'tokenは既に期限切れ']);
        }catch(Exception $e) {  //他のエラー
            return json(['code'=>404,'msg'=>'不正なリクエスト']);
        }catch(\UnexpectedValueException $e) {  //他のエラー
            return json(['code'=>404,'msg'=>'不正なリクエスト']);
        } catch(\DomainException $e) {  //他のエラー
            return json(['code'=>404,'msg'=>'不正なリクエスト']);
        }

    }
	
	
    /**
     * @api {post} /demo/login メンバーログイン
     * @apiDescription システムログインインターフェース、トークンを返し、認証が必要なインターフェースの操作に使用します

     * @apiParam (リクエストパラメータ：) {string}             username ログインユーザー名
     * @apiParam (リクエストパラメータ：) {string}             password ログインパスワード

     * @apiParam (レスポンスフィールド：) {string}             token    トークン

     * @apiSuccessExample {json} 成功例
     * {"code":0,"msg":"ログイン成功","time":1627374739,"data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhcGkuZ291Z3VjbXMuY29tIiwiYXVkIjoiZ291Z3VjbXMiLCJpYXQiOjE2MjczNzQ3MzksImV4cCI6MTYyNzM3ODMzOSwidWlkIjoxfQ.gjYMtCIwKKY7AalFTlwB2ZVWULxiQpsGvrz5I5t2qTs"}}
     * @apiErrorExample {json} 失敗例
     * {"code":1,"msg":"アカウントまたはパスワードが間違っています","time":1627374820,"data":[]}
     */
	
     public function login()
     {
         $param = get_params();
         if (empty($param['username']) || empty($param['password'])) {
             $this->apiError('パラメータエラー');
         }
         // ユーザー名とパスワードを検証する
         $user = Db::name('Admin')->where(['username' => $param['username']])->find();
         if (empty($user)) {
             $this->apiError('アカウントまたはパスワードが間違っています');
         }
         $param['pwd'] = set_password($param['password'], $user['salt']);
         if ($param['pwd'] !== $user['pwd']) {
             $this->apiError('アカウントまたはパスワードが間違っています');
         }
         if ($user['status'] == -1) {
             $this->apiError('このユーザーはログインが禁止されています、プラットフォームにお問い合わせください');
         }
         $data = [
             'last_login_time' => time(),
             'last_login_ip' => request()->ip(),
             'login_num' => $user['login_num'] + 1,
         ];
        $res = Db::name('Admin')->where(['id' => $user['id']])->update($data);
        if ($res) {
            $token = self::getToken($user['id']);
            $this->apiSuccess('ログイン成功', ['token' => $token]);
        }
    }
    /**
     * @api {post} /index/demo テストページ
     * @apiDescription 記事リスト情報を返す

     * @apiParam (リクエストパラメータ:) {string}  token トークン

     * @apiSuccessExample {json} レスポンスデータの例
     * {"code":1,"msg":"","time":1563517637,"data":{"id":13,"email":"test110@qq.com","password":"e10adc3949ba59abbe56e057f20f883e","sex":1,"last_login_time":1563517503,"last_login_ip":"127.0.0.1","qq":"123455","mobile":"","mobile_validated":0,"email_validated":0,"type_id":1,"status":1,"create_ip":"127.0.0.1","update_time":1563507130,"create_time":1563503991,"type_name":"登録会員"}}
     */
    public function test(Request $request)
    {
	    $uid = JWT_UID;
      $userInfo = Db::name('Admin')->where(['id' => $uid])->find();
       $this->apiSuccess('リクエスト成功', ['user' => $userInfo]);
    }
}
