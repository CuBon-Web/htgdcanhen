<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use App\models\Quiz\Questions;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImportExcel implements ToModel, WithStartRow
{
    /**
    * @param Collection $collection
    */
    public function startRow(): int
    {
        return 2;
    }
    public function model(array  $row)
    {
        $query = new Questions();
            $query->name = $row[2];
            $query->sub_name = $row[3];
            $query->sub_explain = $row[12];
            $query->title_review = $row[1];
            $query->status = (int)$row[16];
            // $query->image = $request->image;
            $query->part_id = (int)$row[15];
            $query->exam_id = (int)$row[14];
            $query->poins = 5;
            // $query->audio = $request->audio;

            $query->option_1=$row[4];
            $query->option_2=$row[5];
            $query->option_3=$row[6];
            $query->option_4=$row[7];

            $query->sub_option_1 = $row[8];
            $query->sub_option_2 = $row[9];
            $query->sub_option_3 = $row[10];
            $query->sub_option_4 = $row[11];

            $query->ans = $row[13];
            if($row[13] == 'option_1'){
                $query->ans_text = $row[4];
            }elseif($row[13] == 'option_2'){
                $query->ans_text = $row[5];
            }elseif($row[13] == 'option_3'){
                $query->ans_text = $row[6];
            }else{
                $query->ans_text = $row[7];
            }
            $query->numerical_order = (int)$row[0];
           
            $query->sub_options=json_encode(array('option1'=>$row[8],'option2'=>$row[9],'option3'=>$row[10],'option4'=>$row[11]));
            $query->options=json_encode(array('option1'=>$row[4],'option2'=>$row[5],'option3'=>$row[6],'option4'=>$row[7]));
            $query->save();
            return;
    }
}
