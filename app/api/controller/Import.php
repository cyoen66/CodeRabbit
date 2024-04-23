<?php
/**
 * @copyright Copyright (c) 2021 勾股工作室
 * @license https://opensource.org/licenses/GPL-3.0
 * @link https://www.gougucms.com
 */
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;
use app\user\model\Admin;
use app\customer\model\Customer;
use avatars\MDAvatars;
use Overtrue\Pinyin\Pinyin;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as Shared;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class Import extends BaseController
{
    //アバター生成
    public function to_avatars($char)
    {
        $defaultData = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'S', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            '零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '拾',
            '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
        if (isset($char)) {
            $Char = $char;
        } else {
            $Char = $defaultData[mt_rand(0, count($defaultData) - 1)];
        }
        $OutputSize = min(512, empty($_GET['size']) ? 36 : intval($_GET['size']));

        $Avatar = new MDAvatars($Char, 256, 1);
        $avatar_name = '/avatars/avatar_256_' . set_salt(10) . time() . '.png';
        $path = get_config('filesystem.disks.public.url') . $avatar_name;
        $res = $Avatar->Save('.' . $path, 256);
        $Avatar->Free();
        return $path;
    }
	
    //ログイン名の検証
    public function check_name($name,$arr)
    {
        if(in_array($name,$arr)){
			$name = $this->check_name($name.'1',$arr);
		}
		return $name;       
    }
	
	//従業員のインポート
	public function import_admin(){
        // フォームからアップロードされたファイルを取得
        $file[]= request()->file('file');
		if($this->uid>1){
			return to_assign(1,'この操作はスーパー管理者のみが権限を持つことができます');
		}
        try {
            //  ファイルのサイズ、名前などを検証する
            validate(['file' => 'filesize:51200|fileExt:xls,xlsx'])->check($file);
			// 日付のプレフィックス
			 $dataPath = date('Ym');
			 $md5 = $file[0]->hash('md5');
			 $savename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file[0], function () use ($md5) {
				 return $md5;
			 });
            $fileExtendName = substr(strrchr($savename, '.'), 1);
            //  XlsとXlsxの2つの形式があります
            if ($fileExtendName == 'xlsx') {
                $objReader = IOFactory::createReader('Xlsx');
            } else {
                $objReader = IOFactory::createReader('Xls');
            }
            $objReader->setReadDataOnly(TRUE);
			$path = get_config('filesystem.disks.public.url');
            // ファイルを読み込む。tp6では、デフォルトでアップロードされたファイルはruntimeディレクトリの該当する場所にありますが、実際の状況に合わせて自分で変更してください。
            $objPHPExcel = $objReader->load('.'.$path . '/' .$savename);
            $sheet = $objPHPExcel->getSheet(0);   //excelの最初のシート
            $highestRow = $sheet->getHighestRow();       // 総行数を取得
            $highestColumn = $sheet->getHighestColumn();   // 総列数を取得
            Coordinate::columnIndexFromString($highestColumn);
            $lines = $highestRow - 1;
            if ($lines <= 0) {
				return to_assign(1, 'データは空です');
                exit();
            }
			$sex_array=['未知','男','女'];
			$type_array=['未知','正式','試用','インターンシップ'];
			$mobile_array = Db::name('Admin')->where([['status','>=',0]])->column('mobile');
			$email_array = Db::name('Admin')->where([['status','>=',0]])->column('email');
			$username_array = Db::name('Admin')->where([['status','>=',0]])->column('username');
			$department_array = Db::name('Department')->where(['status' => 1])->column('title', 'id');
			$position_array = Db::name('Position')->where(['status' => 1])->column('title', 'id');
			//excel表をループして配列にまとめます。キーが指定されていない場合、$data[i][j]となります。
			$pinyin = new Pinyin();
			for($j = 3; $j <= $highestRow; $j++) {
				$salt = set_salt(20);
				$reg_pwd  = '123456';
				$name = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
				if(empty($name)){
					continue;
				}
				$char = mb_substr($name, 0, 1, 'utf-8');
				$sex = arraySearch($sex_array,$objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue());
				$department = arraySearch($department_array,$objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue());
				$position = arraySearch($position_array,$objPHPExcel->getActiveSheet()->getCell("f" . $j)->getValue());
				$type = arraySearch($type_array,$objPHPExcel->getActiveSheet()->getCell("G" . $j)->getValue());
				$pinyinname = $pinyin->name($name,PINYIN_UMLAUT_V);
				$username = implode('', $pinyinname);
				$mobile = $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue();
				$email = $objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue();
				$file_check['mobile'] = $mobile;
				$file_check['email'] = $email;
				$validate_mobile = \think\facade\Validate::rule([
					'mobile' => 'require|mobile',
				]);
				$validate_email = \think\facade\Validate::rule([
					'email' => 'email',
				]);
				if (!$validate_mobile->check($file_check)) {
					return to_assign(1, '第'.($j - 2).'行の携帯電話番号の形式が正しくありません');
				}
				else{
					if(in_array($mobile,$mobile_array)){
						return to_assign(1, '第'.($j - 2).'行の携帯電話番号は既に存在するか重複しています');
					}
					else{
						array_push($mobile_array,$mobile);
					}
				}
				if(!empty($email)){
					if (!$validate_email->check($file_check)) {
						return to_assign(1, '第'.($j - 2).'行の電子メールの形式が正しくありません');
					}
					else{
						if(in_array($email,$email_array)){
							return to_assign(1, '第'.($j - 2).'行の電子メールは既に存在するか重複しています');
						}
						else{
							array_push($email_array,$email);
						}
					}
				}
				else{
					$email='';
				}
				if(empty($department)){
					return to_assign(1, '第'.($j - 2).'行の所属部門が正しくありません');
				}
				if(empty($position)){
					return to_assign(1, '第'.($j - 2).'行の役職が正しくありません');
				}
                $data[$j - 3] = [		
                    'name' => $name,
                    'nickname' => $name,
                    'mobile' => $mobile,
                    'email' => $email,
                    'sex' => $sex,
                    'did' => $department,
                    'position_id' => $position,
                    'type' => $type,
					'entry_time' => Shared::excelToTimestamp($objPHPExcel->getActiveSheet()->getCell("H" . $j)->getValue(),'Asia/Shanghai'),
					'username' => $this->check_name($username,$username_array),
                    'salt' => $salt,
					'pwd' => set_password($reg_pwd, $salt),
                    'reg_pwd' => $reg_pwd,
                    'thumb' => $this->to_avatars($char)
                ];
            }
           //dd($data);exit;
            // データを一括追加
            if ((new Admin())->saveAll($data)) {
                return to_assign(0, 'インポートに成功しました');
            }
			else{
				return to_assign(1, 'インポートに失敗しました。Excelファイルを確認してください');
			}
        } catch (\think\exception\ValidateException $e) {
			return to_assign(1, $e->getMessage());
        }
    }
	
	//顧客のインポート
	public function import_customer(){
        // フォームのアップロードファイルを取得する
        $file[]= request()->file('file');
		
		$param = get_params();
		$type = 'sea';
		if(isset($param['type'])){
			$type = $param['type'];
		}
		try {
			// ファイルのサイズ、名前などが正しいかを検証する
			validate(['file' => 'filesize:51200|fileExt:xls,xlsx'])->check($file);
			// 日付のプレフィックス
			 $dataPath = date('Ym');
			 $md5 = $file[0]->hash('md5');
			 $savename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file[0], function () use ($md5) {
				 return $md5;
			 });
			$fileExtendName = substr(strrchr($savename, '.'), 1);
			// XlsおよびXlsxの2つの形式があります
			if ($fileExtendName == 'xlsx') {
				$objReader = IOFactory::createReader('Xlsx');
			} else {
				$objReader = IOFactory::createReader('Xls');
			}
			$objReader->setReadDataOnly(TRUE);
			$path = get_config('filesystem.disks.public.url');
			// ファイルの読み込み。tp6では、デフォルトでアップロードされたファイルはruntimeの対応するディレクトリにあります。実際の状況に合わせて変更してください。
			$objPHPExcel = $objReader->load('.'.$path . '/' .$savename);
			//$objPHPExcel = $objReader->load('./storage/202209/d11544d20b3ca1c1a5f8ce799c3b2433.xlsx');
			$sheet = $objPHPExcel->getSheet(0);   //excelの最初のシート
			$highestRow = $sheet->getHighestRow();       // 行数を取得
			$highestColumn = $sheet->getHighestColumn();   // 列数を取得
			Coordinate::columnIndexFromString($highestColumn);
			$lines = $highestRow - 1;
			if ($lines <= 0) {
				return to_assign(1, 'データは空にできません');
				exit();
			}
			$name_array = []; 
			$source_array = Db::name('CustomerSource')->where(['status' => 1])->column('title', 'id');
			$grade_array = Db::name('CustomerGrade')->where(['status' => 1])->column('title', 'id');
			$industry_array = Db::name('Industry')->where(['status' => 1])->column('title', 'id');
					
			//excel表を読み取り、配列にまとめます。キーを指定しない場合は$data[i][j]を使用します。
			for ($j = 3; $j <= $highestRow; $j++) {
				$file_check = [];
				$name = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
				if(empty($name)){
					continue;
				}
				$count_name =Db::name('Customer')->where('name',$name)->count();
				if($count_name>0){
					return to_assign(1, '第'.($j - 2).'行の顧客名が既に存在しています');
				}
				if(in_array($name,$name_array)){
					return to_assign(1, 'アップロードされたファイルには同じ顧客名が存在します。削除してから操作してください');
				}
				array_push($name_array,$name);
				$source_id = arraySearch($source_array,$objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue());
				$grade_id = arraySearch($grade_array,$objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue());
				$industry_id = arraySearch($industry_array,$objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue());	
				
				$c_name = $objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue();
				$c_mobile = $objPHPExcel->getActiveSheet()->getCell("F" . $j)->getValue();
				$file_check['c_mobile'] = $c_mobile;
				$tax_num = $objPHPExcel->getActiveSheet()->getCell("G" . $j)->getValue();
				$bank = $objPHPExcel->getActiveSheet()->getCell("H" . $j)->getValue();
				$bank_sn = $objPHPExcel->getActiveSheet()->getCell("I" . $j)->getValue();
				$file_check['bank_sn'] = $bank_sn;
				$bank_no = $objPHPExcel->getActiveSheet()->getCell("K" . $j)->getValue();				
				$cperson_mobile = $objPHPExcel->getActiveSheet()->getCell("K" . $j)->getValue();				
				$address = $objPHPExcel->getActiveSheet()->getCell("L" . $j)->getValue();
				$content = $objPHPExcel->getActiveSheet()->getCell("M" . $j)->getValue();
				$market = $objPHPExcel->getActiveSheet()->getCell("N" . $j)->getValue();
				if(empty($c_name)){
					return to_assign(1, '第'.($j - 2).'行の顧客連絡先の名前が入力されていません');
				}
				if(empty($c_mobile)){
					return to_assign(1, '第'.($j - 2).'行の顧客連絡先の携帯電話番号が入力されていません');
				}
				$validate_mobile = \think\facade\Validate::rule([
					'c_mobile' => 'mobile',
				]);
				if (!$validate_mobile->check($file_check)) {
					return to_assign(1, '第'.($j - 2).'行の顧客連絡先の携帯電話番号の形式が正しくありません');
				}
				if(empty($source_id)){
					return to_assign(1, '第'.($j - 2).'行の顧客の情報元が正しくありません');
				}
				if(empty($grade_id)){
					return to_assign(1, '第'.($j - 2).'行の顧客の評価が正しくありません');
				}
				if(empty($industry_id)){
					return to_assign(1, '第'.($j - 2).'行の所属業界が正しくありません');
				}
				if(empty($tax_num)){
					$tax_num='';
				}
				if(empty($bank)){
					$bank='';
				}
				$validate_bank = \think\facade\Validate::rule([
					'bank_sn' => 'number',
				]);
				if(!empty($bank_sn)){
					if (!$validate_bank->check($file_check)) {
						return to_assign(1, '第'.($j - 2).'行の銀行口座番号の形式が正しくありません');
					}
				}
				else{
					$bank_sn='';
				}
				if(empty($bank_no)){
					$bank_no='';
				}
				if(empty($cperson_mobile)){
					$cperson_mobile='';
				}
				if(empty($address)){
					$address='';
				}
				if(empty($content)){
					$content='';
				}
				if(empty($market)){
					$market='';
				}
				$belong_uid = 0;
				$belong_did = 0;
				if($type != 'sea'){
					$belong_uid = $this->uid;
					$belong_did = $this->did;
				}
				$data[$j - 3] = [		
					'name' => $name,
					'source_id' => $source_id,
					'grade_id' => $grade_id,
					'industry_id' => $industry_id,
					'tax_num' => $tax_num,
					'bank' => $bank,
					'bank_sn' => $bank_sn,
					'bank_no' => $bank_no,
					'cperson_mobile' => $cperson_mobile,
					'address' => $address,
					'content' => $content,
					'market' => $market,
					'admin_id' => $this->uid,
					'belong_uid' => $belong_uid,
					'belong_did' => $belong_did,
					'c_mobile' => $c_mobile,
					'c_name' => $c_name,
					'create_time' => time()
				];
			}
			//dd($data);exit;
			// データを一括追加する
			$count=0;
			foreach ($data as $a => $aa) {	
				$cid = Customer::strict(false)->field(true)->insertGetId($aa);
				if($cid>0){
					$contact = [
						'name' => $aa['c_name'],
						'mobile' => $aa['c_mobile'],
						'sex' => 1,
						'cid' => $cid,
						'is_default' => 1,
						'create_time' => time(),
						'admin_id' => $this->uid
					];
					Db::name('CustomerContact')->strict(false)->field(true)->insert($contact);
					$count++;
				}
			}
			return to_assign(0, '合計'.$count.'件の顧客データが正常にインポートされました');
		} catch (\think\exception\ValidateException $e) {
			return to_assign(1, $e->getMessage());
		}
	}
	
}
