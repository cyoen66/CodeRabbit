<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */

namespace app\api\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Request;
use think\Response;

class Auth
{
    public function handle($request, \Closure $next)
    {
        $token = Request::header('Token');
        if ($token) {
            if (count(explode('.', $token)) != 3) {
                return json(['code'=>404,'msg'=>'不正なリクエスト']);
            }
			$config = get_system_config('token');
			//var_dump($config);exit;
            try {
				JWT::$leeway = 60; // 現在の時刻から60秒差を許容する
					$decoded = JWT::decode($token, new Key($config['secrect'], 'HS256')); // HS256方式、ここは
					//return (array)$decoded;
					$decoded_array = json_decode(json_encode($decoded),TRUE);
					$jwt_data = $decoded_array['data'];
					//$request->uid = $jwt_data['userid'];
					define('JWT_UID', $jwt_data['userid']);
					$response = $next($request);
					return $response;
					//return $next($request);
				} catch (\Firebase\JWT\SignatureInvalidException $e) { // 署名が正しくない
					return json(['code'=>403,'msg'=>'署名エラー']);
				} catch (\Firebase\JWT\BeforeValidException $e) { // 署名が有効になる前の時間
					return json(['code'=>401,'msg'=>'トークンが無効です']);
				} catch (\Firebase\JWT\ExpiredException $e) { // トークンの有効期限切れ
					return json(['code'=>401,'msg'=>'トークンの有効期限が切れています']);
				} catch (Exception $e) { // その他のエラー
					return json(['code'=>404,'msg'=>'不正なリクエスト']);
				} catch (\UnexpectedValueException $e) { // その他のエラー
					return json(['code'=>404,'msg'=>'不正なリクエスト']);
				} catch (\DomainException $e) { // その他のエラー
					return json(['code'=>404,'msg'=>'不正なリクエスト']);
				}
        } else {
            return json(['code'=>404,'msg'=>'tokenは空にできません']);
        }
        return $next($request);
    }
}
