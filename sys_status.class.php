<?php
if(!defined("__ZBXE__")) exit();
class sys_status extends WidgetHandler {

    /**
	 * 제작자 : 키큰아이 지원 : CMD,BNU
     * @brief 위젯의 실행 부분
     *
     * ./widgets/위젯/conf/info.xml 에 선언한 extra_vars를 args로 받는다
     * 결과를 만든후 print가 아니라 return 해주어야 한다
     **/
    function proc($args) {		
            //var_dump($args);exit; //에러확인
			// 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);

			// HOME데이터 사용량 조회
			$du = `du -sk` ; // 유저 사용량 조회
			$du = $du/1000; // MB단위로 변환
			$data_hard=$du; // 유저 사용량 출력

			if($args->data_print == 'Y'){
			if(strlen($args->data_max) > 0)  {$data_hard_max  = $args->data_max;}else{$data_hard_max  = 1000;}
			Context::set('data_max', $data_hard_max); // DB 콘텍스트(패스워드)
			
		    $data_info->data_hard = $data_hard; // 홈 데이터 사용량 조회
			$data_info->data_hard2 = $data_hard_max-$data_hard; // 홈 데이터 사용량 조회
			$data_info->data_hard3 = $data_hard_max; // 홈 데이터 사용량 조회
			$data_info->data_type = $args->data_type; // 위젯 스킨설정 DATA
			}
			
			if($args->db_print == 'Y'){
			$oDB = &DB::getInstance();
			$result = $oDB->_query('SHOW TABLE STATUS');
			$data = $oDB->_fetch($result);
			$db_busy = 0;
			foreach($data as $table){
			$db_busy += $table->Data_length + $table->Index_length;}
			
			$mega= 1048576; //MB단위로 숫자 변환
			
			$db_hard=round($db_busy/$mega,3);
			
			$data_info->db_hard = $db_hard;// 디비 데이터 사용량 조회 + 소숫점3자리
			$data_info->db_type = $args->db_type; // 위젯 스킨설정 SQL
			}
			
            Context::set('data_info', $data_info); // 템플릿 콘텍스트(위젯정보)
			Context::set('colorset', $args->colorset); //컬러셋콘텍스트(위젯컬러셋)
			
			$data_info->admin_print = $args->admin_print;
			Context::set('args', $args);
			
            // 템플릿 파일을 지정
			$tpl_file = 'sys';

			// 템플릿 컴파일 //스킨 widget.html
            $oTemplate = &TemplateHandler::getInstance();
            return $oTemplate->compile($tpl_path, $tpl_file);
	}
}
?>