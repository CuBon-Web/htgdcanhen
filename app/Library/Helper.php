<?php
if (!function_exists('handle')) {
    function handle($request)
    {
        $input = $request->all();
        if ($input) {
            array_walk_recursive($input, function (&$item) {
                $item = trim($item);
                $item = ($item == "") ? null : $item;
            });
            $request->merge($input);
        }
        return $request->all();
    }
}
if(!function_exists('uploadImage')){
    function uploadImage($img){
        $nameAvatar = rand().$img->getClientOriginalName();
        $img->move('uploads/customer/', $nameAvatar);
        return '/uploads/customer/'.$nameAvatar;
    }
}
if(!function_exists('stripVN')){
    function stripVN($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
    
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        return $str;
    }
}
if (!function_exists('getConfig')) {
    function getConfig($key)
    {
        $setting = new \App\models\Client\Setting();
        return $setting->getValue($key);
    }
}

if (!function_exists('getLanguage')) {
    function getLanguage($key)
    {
        $session =  Session::get('locale');
        if($session != null){
            $code = Session::get('locale');
        }else{
            Session::put('locale', app()->getLocale());
        }
        $code = Session::get('locale');
        $lang = new \App\models\frontend\LanguageStaticByLang();
        return $lang->getLanguageValue($key, $code);
    }
}
if(!function_exists('toArrayLanguage')){
    function toArrayLanguage($value){
        $session =  Session::get('locale');
        if($session != null){
            $code = Session::get('locale');
        }else{
            Session::put('locale', app()->getLocale());
        }
        $code = Session::get('locale');
        $arr = [];
        $obj = new stdClass();
        $obj->lang_code = $code;
        $obj->content = $value;
        $arr[] = $obj;
        return json_encode($arr);
    }
}
if(!function_exists('languageName')){
    function languageName($arrName){
        $arr = json_decode($arrName);
        $session =  Session::get('locale');
        if($session != null){
            $code = Session::get('locale');
        }else{
            Session::put('locale', app()->getLocale());
        }
        $code = Session::get('locale');
        foreach($arr as $item){
            if($item->lang_code == $code){
                return $item->content;
            }
        }
    }
}
if(!function_exists('checkExistCart')){
    function checkExistCart($proid){
        $cart = Session::get('cart', []);
        foreach($cart as $item){
            if($item['idpro'] == $proid){
                return true;
            }else{
                return false;
            }
        }
    }
} 

if(!function_exists('checkBillCourse')){
    function checkBillCourse($customer_id, $course_id){
       $billCourse = new \App\models\BillCourse();
        $result = $billCourse->checkBillStatus($customer_id, $course_id);
        return $result;
    }
}
// if(!function_exists('getMyAns')){
//     function getMyAns($numerical_order,$result) {
//         $result_json = json_decode($result['result_json']);
//         foreach($result_json as $item){
//             if($item->numerical_order == $numerical_order){
//                 return true;
//             }else{
//                 return false;
//             }
//         }
//     }
// }
if (!function_exists('getMenu')) {
    function getMenu($position)
    {
        $data = [];
        $menu = new \App\models\Client\Menu();
        $menuGroup = new \App\models\Client\MenuGroup();
        $result = $menuGroup->getMenuPosition($position);
        foreach ($result as $i => $item) {
            $data[$i] = $menu->getTree(1, $item['id'], getConfig('config_logo'));
        }
        return $data;
    }
}

if (!function_exists('getActiveLanguages')) {
    function getActiveLanguages()
    {
        $language = new \App\models\frontend\Language();
        $data = $language->getLanguages();
        return $data;
    }
}

if (!function_exists('getCurrentLang')) {

    function getCurrentLang()
    {
        $locale = request()->cookie('locale');
        if (!isset($locale)) {
            $locale = getConfig('config_language');
        }
        return $locale;
    }
}

if (!function_exists('shorten_string')) {
    function shorten_string($string, $words = 50)
    {
        $string = preg_replace('/(?<=\S,)(?=\S)/', ' ', $string);
        $string = str_replace("\n", " ", $string);
        $array = explode(" ", $string);
        if (count($array) <= $words) {
            $retval = $string;
        } else {
            array_splice($array, $words);
            $retval = implode(" ", $array) . " ...";
        }
        return $retval;
    }
}

if(!function_exists('to_slug')){
    function to_slug($str){
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }
}

if (!function_exists('resizeImage')) {
    function resizeImage($originalImagePath, $newImagePath, $width = 50, $height = 50)
    {
        $name = preg_split('/[\/]+/', $originalImagePath);
        if (!file_exists('cache/' . $name[0] . '/' . $name[1])) {
            mkdir('cache/' . $name[0] . '/' . $name[1], 0777, true);
        }
        if (file_exists($originalImagePath)) {
            if (!file_exists($newImagePath)) {
                $image = imagecreatefromstring(file_get_contents($originalImagePath));
                $thumb_width = $width;
                $thumb_height = $height;
                $width = imagesx($image);
                $height = imagesy($image);
                $original_aspect = $width / $height;
                $thumb_aspect = $thumb_width / $thumb_height;
                if ($original_aspect >= $thumb_aspect) {
                    $new_height = $thumb_height;
                    $new_width = $width / ($height / $thumb_height);
                } else {
                    $new_width = $thumb_width;
                    $new_height = $height / ($width / $thumb_width);
                }
                $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
                imagecopyresampled($thumb,
                    $image,
                    0 - ($new_width - $thumb_width) / 2,
                    0 - ($new_height - $thumb_height) / 2,
                    0, 0,
                    $new_width, $new_height,
                    $width, $height);
                imagejpeg($thumb, $newImagePath, 80);
            }
        }
    }
}

if (!function_exists('formatTime')) {
    function formatTime($seconds) {
        $hours   = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs    = $seconds % 60;

        if ($hours > 0) {
            return sprintf("%02d giờ %02d phút %02d giây", $hours, $minutes, $secs);
        } else {
            return sprintf("%02d phút %02d giây", $minutes, $secs);
        }
    }
}

if (!function_exists('calculateExamTimeStatus')) {
    function calculateExamTimeStatus($start_time, $end_time) {
        if (!$start_time || !$end_time) {
            return [
                'status' => 'expired',
                'text' => ''
            ];
        }

        // Parse thời gian nếu là string
        $start_time = \Carbon\Carbon::parse($start_time);
        $end_time = \Carbon\Carbon::parse($end_time);
        $now = \Carbon\Carbon::now();

        if ($now->lt($start_time)) {
            // Thời gian hiện tại < start_time → Chưa bắt đầu
            $diffMinutes = $now->diffInMinutes($start_time, false);
            $hours = intdiv($diffMinutes, 60);
            $minutes = $diffMinutes % 60;
            
            // Format: "X giờ Y phút" hoặc chỉ "Y phút" nếu < 1 giờ
            if ($hours > 0) {
                $timeText = $hours . ' giờ' . ($minutes > 0 ? ' ' . $minutes . ' phút' : '');
            } else {
                $timeText = $minutes . ' phút';
            }
            
            return [
                'status' => 'not_started',
                'text' => $timeText
            ];
        } elseif ($now->gte($start_time) && $now->lte($end_time)) {
            // Thời gian hiện tại nằm trong khoảng [start_time, end_time] → Đang trong thời gian làm bài
            $diffMinutes = $now->diffInMinutes($end_time, false);
            
            if ($diffMinutes <= 0) {
                // Đã hết thời gian (trường hợp edge case)
                return [
                    'status' => 'expired',
                    'text' => ''
                ];
            }
            
            $hours = intdiv($diffMinutes, 60);
            $minutes = $diffMinutes % 60;
            
            // Format: "X giờ Y phút" hoặc chỉ "Y phút" nếu < 1 giờ
            if ($hours > 0) {
                $timeText = $hours . ' giờ' . ($minutes > 0 ? ' ' . $minutes . ' phút' : '');
            } else {
                $timeText = $minutes . ' phút';
            }
            
            return [
                'status' => 'active',
                'text' => $timeText
            ];
        } else {
            // Thời gian hiện tại > end_time → Đã kết thúc
            return [
                'status' => 'expired',
                'text' => ''
            ];
        }
    }
}