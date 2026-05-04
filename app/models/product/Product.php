<?php

namespace App\models\product;

use Illuminate\Database\Eloquent\Model;
use App\models\language\Language;
use Image,Auth;
use App\Customer;
use App\models\product\Category;
use App\models\product\TypeProduct;
use  App\models\product\TypeProductTwo;
class Product extends Model
{
    protected $table = "products";
    public function rule()
    {
        return [
            // 'name'  =>   'unique:products,name'
        ];
    }
    public function customer()
    {
        return $this->hasOne(Customer::class,'id','user_id');
    }
    public function cate()
    {
        return $this->hasOne(Category::class,'id','category');
    }
    public function typecate()
    {
        return $this->hasOne(TypeProduct::class,'id','type_cate');
    }
    public function createOrEdit($request)
    {
        $cat = Category::where('id',$request->category)->first('slug');
        $typecat = TypeProduct::where('id',$request->type_cate)->first('slug');
        $typetwo = TypeProductTwo::where('id',$request->type_two)->first('slug');
        $id = $request->id;
        if($id){
            $query = Product::find($id);
            if($query){
                $query->name = ($request->name);
                $query->slug = to_slug($request->name);
                $query->price = $request->price != null ? $request->price : 0;
                $query->discount = $request->discount != null ? $request->discount : 0;
                $query->images = $request->images;
                $query->description = $request->description;
                $query->content = $request->content;
                $query->size = json_encode($request->size); //Nội dung khóa học

                $query->category = $request->category;
                $query->type_two = $request->type_two;
                $query->preserve = json_encode($request->preserve);// Khóa học bao gồm
                // $query->tags = json_encode($request->tags);
                $query->cate_slug = $cat ? $cat->slug : '';
                $query->type_slug = $typecat ? $typecat->slug : '';
                $query->type_two_slug = $typetwo ? $typetwo->slug : '';
                $query->ingredient = $request->ingredient;// số bài học
                $query->type_cate = $request->type_cate;
                
                $query->species = json_encode($request->species); // câu hỏi thường gặp
                $query->origin = $request->origin;
                $query->thickness = $request->thickness;
                
                $query->status = $request->status;
                $query->discountStatus = $request->discountStatus;
                $query->home_status = $request->home_status;
                $query->save();
            }
            return $query;
        }else{
            $query = new Product();
            $query->name = ($request->name);
            $query->slug = to_slug($request->name);
            $query->price = $request->price != null ? $request->price : 0;
            $query->discount = $request->discount != null ? $request->discount : 0;
            $query->images = $request->images;
            $query->description = $request->description;
            $query->content = $request->content;
            $query->size = json_encode($request->size); //Nội dung khóa học

            $query->category = $request->category;
            $query->type_two = $request->type_two;
            $query->preserve = json_encode($request->preserve);// Khóa học bao gồm
            // $query->tags = json_encode($request->tags);
            $query->cate_slug = $cat ? $cat->slug : '';
            $query->type_slug = $typecat ? $typecat->slug : '';
            $query->type_two_slug = $typetwo ? $typetwo->slug : '';
            $query->ingredient = $request->ingredient;// số bài học
            $query->type_cate = $request->type_cate;
            
            $query->species = json_encode($request->species); // câu hỏi thường gặp
            $query->origin = $request->origin;
            $query->thickness = $request->thickness;
            
            $query->status = $request->status;
            $query->discountStatus = $request->discountStatus;
            $query->home_status = $request->home_status;
            $query->save();
            return $query;
        }
    }
    public function createClient($request)
    {
        $images = $request->file('images');
        $images_data = [];
        $query = new Product();
        $query->name = toArrayLanguage($request->name);
        $query->slug = to_slug($request->name[0]['content']);

        $query->description = toArrayLanguage($request->description);
        $query->uses = toArrayLanguage($request->uses);
        $query->relationship = $request->relationship;
        $query->category = $request->category;
        $query->type_cate = $request->type_cate;
        $query->management_costs = $request->management_costs;
        $query->address = $request->address; 
        $query->district = $request->district;
        $query->author = Auth::guard('customer')->user()->id;
        $query->content = toArrayLanguage($request->content); 
        
        $query->status = $request->status; //0-Sẵn sàng giao dịch 1-Ngừng giao dịch
        $query->acreage = $request->acreage;
        
        
       
        $query->percent_yourself = $request->percent_yourself; 
        $query->cost_investment = $request->cost_investment; 
        $query->video = $request->video;
        if($request->hasFile('images')){
            foreach($images as $image){
                $name=time().$image->getClientOriginalName();
                $destinationPath = 'uploads/real_estate/' . $name;
                $img = Image::make($image->getRealPath());
                $img->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save('uploads/real_estate/'.$name);
                $images_data[] = $name;
            }
        }
        $query->images=json_encode($images_data);
        $query->save();
        return $query;
    }
}
