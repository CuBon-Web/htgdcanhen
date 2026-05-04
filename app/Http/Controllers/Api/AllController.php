<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Librarys;
use App\models\MessContact;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportExcel;
use App\Imports\ImportExcelDocs;
use Illuminate\Support\Facades\Storage;
use App\Exports\ExportExcelCustomer;
use File,Http,Str;
use App\Customer;

class AllController extends Controller
{
    public function exportExcelCustomer(){
        $data = Customer::all();
        return Excel::download(new ExportExcelCustomer($data), 'customers.xlsx');
    }
    /**
     * Helper method để xử lý file upload an toàn
     */
    private function handleFileUpload($file, $allowedTypes = [], $maxSize = 5 * 1024 * 1024, $folder = 'uploads')
    {
        try {
            // Kiểm tra file có tồn tại và hợp lệ không
            if (!$file || !$file->isValid()) {
                return [
                    'success' => false,
                    'error' => 'File upload không hợp lệ'
                ];
            }
            
            // Lấy thông tin file trước khi xử lý
            $fileSize = $file->getSize();
            $fileType = $file->getMimeType();
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Kiểm tra kích thước file
            if ($fileSize > $maxSize) {
                return [
                    'success' => false,
                    'error' => 'Kích thước file không được vượt quá ' . ($maxSize / 1024 / 1024) . 'MB'
                ];
            }
            
            // Kiểm tra loại file nếu có yêu cầu
            if (!empty($allowedTypes) && !in_array($fileType, $allowedTypes)) {
                return [
                    'success' => false,
                    'error' => 'Loại file không được hỗ trợ'
                ];
            }
            
            // Tạo tên file mới
            $filename = Str::uuid() . '.' . $extension;
            $path = public_path($folder);
            
            // Tạo thư mục nếu chưa có
            if (!File::exists($path)) {
                File::makeDirectory($path, 0775, true);
            }
            
            // Di chuyển file
            $moveResult = $file->move($path, $filename);
            
            if (!$moveResult) {
                return [
                    'success' => false,
                    'error' => 'Không thể lưu file'
                ];
            }
            
            return [
                'success' => true,
                'filename' => $filename,
                'size' => $fileSize,
                'type' => $fileType,
                'original_name' => $originalName,
                'url' => '/' . $folder . '/' . $filename
            ];
            
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Lỗi xử lý file: ' . $e->getMessage()
            ];
        }
    }
    
    public function uploadImageCourser(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Không có file ảnh được upload'], 400);
        }
        
        $file = $request->file('image');
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $result = $this->handleFileUpload($file, $allowedTypes, $maxSize, 'khoahocbaogom');
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'url' => $result['url'],
                'filename' => $result['filename'],
                'size' => $result['size'],
                'type' => $result['type'],
                'original_name' => $result['original_name']
            ]);
        } else {
            return response()->json(['error' => $result['error']], 400);
        }
    }
    public function uploadAudio(Request $request){
        if (!$request->hasFile('audio')) {
            return response()->json(['error' => 'Không có file audio được upload'], 400);
        }
        
        $file = $request->file('audio');
        $allowedTypes = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/m4a'];
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        $result = $this->handleFileUpload($file, $allowedTypes, $maxSize, 'audio');
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'url' => $result['url'],
                'filename' => $result['filename'],
                'size' => $result['size'],
                'type' => $result['type'],
                'original_name' => $result['original_name']
            ]);
        } else {
            return response()->json(['error' => $result['error']], 400);
        }
    }
    
    public function uploadQuestionImage(Request $request){
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Không có file ảnh được upload'], 400);
        }
        
        $file = $request->file('image');
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        $result = $this->handleFileUpload($file, $allowedTypes, $maxSize, 'exam_images');
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'url' => $result['url'],
                'filename' => $result['filename'],
                'size' => $result['size'],
                'type' => $result['type'],
                'original_name' => $result['original_name']
            ]);
        } else {
            return response()->json(['error' => $result['error']], 400);
        }
    }
     public function uploadCourser(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'document') {
            if (!$request->hasFile('file')) {
                return response()->json(['error' => 'Không có file được upload'], 400);
            }

            $file = $request->file('file');
            $maxSize = 50 * 1024 * 1024; // 50MB cho document
            
            $result = $this->handleFileUpload($file, [], $maxSize, 'document');
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'url' => $result['url'],
                    'filename' => $result['filename'],
                    'size' => $result['size'],
                    'type' => $result['type'],
                    'original_name' => $result['original_name']
                ]);
            } else {
                return response()->json(['error' => $result['error']], 400);
            }
        }

        // Nếu là video thì tạo Direct Upload URL hoặc TUS URL
        if ($type === 'video') {
            try {
                // Kiểm tra các biến môi trường
                $accountId = env('CLOUDFLARE_STREAM_ACCOUNT_ID');
                $token = env('CLOUDFLARE_STREAM_TOKEN');
                
                if (!$accountId || !$token) {
                    return response()->json([
                        'error' => 'Thiếu cấu hình CLOUDFLARE_STREAM_ACCOUNT_ID hoặc CLOUDFLARE_STREAM_TOKEN'
                    ], 400);
                }

                // Lấy metadata từ request
                $filename = $request->input('filename');
                $filesize = $request->input('filesize');
                $filetype = $request->input('filetype');
                $uploadMethod = $request->input('upload_method', 'direct');

                // Kiểm tra kích thước file
                $maxSize = 500 * 1024 * 1024; // 500MB
                
                if ($filesize > $maxSize) {
                    return response()->json([
                        'error' => 'File video quá lớn. Vui lòng chọn file nhỏ hơn 500MB'
                    ], 400);
                }

                // Quyết định upload method dựa trên file size
                $maxSizeForDirectUpload = 200 * 1024 * 1024; // 200MB
                
                if ($filesize > $maxSizeForDirectUpload || $uploadMethod === 'tus') {
                    // Sử dụng TUS upload cho file lớn
         
                    $headers = [
                        'Authorization' => 'Bearer ' . env('CLOUDFLARE_STREAM_TOKEN'),
                        'Tus-Resumable' => '1.0.0',
                        'Upload-Length' => $filesize,
                        'Upload-Metadata' => 'name ' . base64_encode($filename),
                    ];
                    
                    $response = Http::withHeaders($headers)->post(
                        "https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_STREAM_ACCOUNT_ID') . "/stream",
                        [
                            'meta' => [
                                'name' => $filename,
                            ],
                            'upload' => [
                                'approach' => 'tus',
                                'size' => $filesize,
                            ]
                        ]
                    );
                    
                    $uploadURL = $response->header('Location');
                    $uid = basename(parse_url($uploadURL, PHP_URL_PATH));
                    // dd($uid,$uploadURL);
                    if (isset($uploadURL)) {
                        return response()->json([
                            'success' => true,
                            'uid' => $uid,
                            'uploadMethod' => $filesize > $maxSizeForDirectUpload ? 'tus' : 'direct',
                            'uploadURL' => $uploadURL
                        ]);
                    } else {
                        return response()->json([
                            'error' => 'Không thể lấy upload URL từ response',
                            'response' => $data
                        ], 500);
                    }

                } else {
                    // Sử dụng Direct Upload cho file nhỏ
                    $response = Http::withToken($token)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ])
                        ->post("https://api.cloudflare.com/client/v4/accounts/{$accountId}/stream/direct_upload", [
                            'maxDurationSeconds' => 3600,
                            'requireSignedURLs' => false,
                            'creator' => auth()->id() ?? 'system',
                            'meta' => [
                                'name' => $filename,
                                'size' => $filesize,
                                'type' => $filetype
                            ]
                        ]);
                        if (!$response->successful()) {
                            return response()->json([
                                'error' => 'Không thể tạo upload URL',
                                'details' => $response->json()
                            ], 500);
                        }
                        $data = $response->json();
                        $uploadURL = $data['result']['uploadURL'];
                        if (isset($data['result']['uploadURL']) && isset($data['result']['uid']) ) {
                            return response()->json([
                                'success' => true,
                                'uid' => $data['result']['uid'],
                                'uploadMethod' => $filesize > $maxSizeForDirectUpload ? 'tus' : 'direct',
                                'uploadURL' => $uploadURL
                            ]);
                        } else {
                            return response()->json([
                                'error' => 'Không thể lấy upload URL từ response',
                                'response' => $data
                            ], 500);
                        }
                }

               

                
                
                

            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Lỗi khi tạo upload URL: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json(['error' => 'Kiểu file không hợp lệ'], 400);
    }
        // Xóa video trên Cloudflare Stream
    public function deleteVideoCloudflare(Request $request)
    {
        $uid = $request->input('uid');
        if (!$uid) {
            return response()->json(['error' => 'Thiếu uid'], 400);
        }

        $url = "https://api.cloudflare.com/client/v4/accounts/" . env('CLOUDFLARE_STREAM_ACCOUNT_ID') . "/stream/" . $uid;
        $response = Http::withToken(env('CLOUDFLARE_STREAM_TOKEN'))
            ->delete($url);
        $data = $response->json();

        if ($response->successful() && isset($data['success']) && $data['success'] === true) {
            return response()->json(['message' => 'Xóa video thành công']);
        } else {
            return response()->json(['error' => $data['errors'] ?? 'Xóa thất bại'], 400);
        }
    }

    public function importExcel(Request $request){
        Excel::import(new ImportExcel,$request->file);
        return response()->json([
            'messenge' => 'success'
        ],200);
    }
    public function importExcelDocs(Request $request){
        Excel::import(new ImportExcelDocs,$request->file);
        return response()->json([
            'messenge' => 'success'
        ],200);
    }
    public function uploadImage(Request $request)
    {
        if($imgAvatar = $request->file('img')){
            $nameAvatar = $imgAvatar->getClientOriginalName();
            $fintimg = strpos($nameAvatar, '.jpg');
            if($fintimg == true){
                $nameImg = $request->title_post.'-'.rand().'.jpg';
            }else{
                $nameImg = $request->title_post.'-'.rand().'.png';
            }
            $imgAvatar->move('uploads/images/', $nameImg);
            return response()->json([
                'messenge' => 'success',
                'path' => url('/').'/uploads/images/'.$nameImg
            ],200);
        }else{
            return response()->json([
                'data' => 'fail'
            ],500);
        }
        
    }
    public function uploadImageMulti(Request $request)
    {
        $uploadId = [];
        if($files = $request->file('file')){
            foreach($request->file('file') as $key => $file){
                $name = rand().$file->getClientOriginalName();
                $fielname = $file->move('uploads/imagesMuli/', $name);
                $uploadId[] = url('/').'/uploads/images/'.$name;
            }
        }
        return response()->json([
            'messenge' => 'success',
            'path' => $uploadId
        ],200);
    }
    public function fileStore(Request $request)
    {
        $upload_path = public_path('upload/audio/');
        $file_name = $request->file->getClientOriginalName();
        $generated_new_name =date('Ymd') . time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->move($upload_path, $generated_new_name);
         
        $insert['title'] = $generated_new_name;
        return response()->json([
            'messenge' => 'success',
            'path' => $insert
        ],200);
    }
    public function listMesscontact(Request $request)
    {
        $keyword = $request->keyword;
        if($keyword == ""){
            $data = MessContact::orderBy('id','DESC')->get();
        }else{
            $data = MessContact::where('title', 'LIKE', '%'.$keyword.'%')->orderBy('id','DESC')->get()->toArray();
        }
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
}
