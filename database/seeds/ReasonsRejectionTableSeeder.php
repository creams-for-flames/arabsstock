<?php

use App\Models\RejectionReason;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RejectionReasonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $data = [

            ['type'=>"temporarily",'title'=>"في حال كانت الصور غير واضحة من ناحية المكان و الوصف ",'description_ar'=>"عزيزي المساهم يرجى تزويدنا بمعلومات  اكثرحول الصوره / الفيديو ( عنوان مكان الصوره/ الفيديو + وصف خاص للصورة/ للفيديو)",'description_en'=>"Dear contributor, please provide us with more information about the photo / video (the address of the photo / video location + a special description of the photo / video)"],
            ['type'=>"temporarily",'title'=>"في حال ظهور أشخاص ",'description_ar'=>"عزيزي المساهم نعتذر عن قبول قبول الصوره / الفيديو بسبب ظهورأشخاص ، يرجى إرفاق نموذج موافقة العارض على الظهور , يمكنك إعادة رفع النماذج وإعادة تقديمها مباشرة للمراجعة","description_en"=>"Dear contributor, we apologize for not accepting the photo / video due to the appearance of people, please attach the model consent form for the viewer to appear, you can re-upload the forms and resubmit them directly for review"],
            ['type'=>"permanently",'title'=>"في حال كانت الصور مكررة",'description_ar'=>" عزيزي المساهم نعتذر عن  قبول الصوره ، الصور مكررة , لا يمكن رفع ذات الصور المتشابهة والمتكررة","description_en"=>"Dear contributor, we apologize for not accepting the image. The images are duplicates. The same and similar images cannot be uploaded"],
            ['type'=>"temporarily",'title'=>"في حال ظهور عناوين وشعارات وغيره",'description_ar'=>"عزيزي المساهم نعتذرعن قبول الصوره / الفيديو، بسبب ظهور شعارات وأسماء تجارية تمتلك حقوق ملكية للنشر، يمكنك إعادة رفع النموذج وإعادة تقديمها مباشرة للمراجعة","description_en"=>"Dear contributor, we apologize for not accepting the photo / video, due to the appearance of logos and trade names that own copyrights for publication, you can re-upload the form and re-submit it directly for review"],
            ['type'=>"permanently",'title'=>"في حال كانت الصوره مائله",'description_ar'=>"عزيزي المساهم نعتذرعن قبول الصورة ، لعدم موافقتها مع شروط القبول لعربستوك : بسبب ان ابعاد و اتجاه الصوره  خاطئ ، بامكانك تعديل الابعاد و اعاده ارسال الصوره","description_en"=>"Dear contributor, we apologize for not accepting the image, because it does not agree with Arabstock's conditions of acceptance: because the dimensions and direction of the image are wrong, you can adjust the dimensions and resubmit the image"],
            ['type'=>"permanently",'title'=>"في حال وجود لوجو او توقيع في الصوره ",'description_ar'=>"عزيزي المساهم نعتذر عن قبول الصوره / الفيديو ،  بسبب وجود لوجو شخصي مخالف لحقوق ملكية النشر","description_en"=>"Dear contributor, we apologize for not accepting the photo / video, due to the presence of a personal logo that violates copyrights"],
            ['type'=>"permanently",'title'=>"في حال كانت الصوره لا تدل على دول الخليج",'description_ar'=>"عزيزي المساهم نعتذر عن قبول الصوره / الفيديو ، الصوره / الفيديو لا تتناسب مع شروط القبول الخاصه بعربستوك  ، يجب ان تكون الصوره / الفيديو لها اختصاص بالمحتوى الخليجي","description_en"=>"Dear contributor, we apologize for not accepting the photo / video. The photo / video does not comply with the conditions of acceptance of ArabStock. The photo / video must be related to Gulf content."],
            ['type'=>"permanently",'title'=>"في حال كانت جودة الصوره سيئة",'description_ar'=>"عزيزي المساهم نعتذرعن قبول الصورة / الفيديو، جودة الصورة / الفيديو لا  تناسب محتوى عربستوك","description_en"=>"Dear contributor, we apologize for not accepting the photo/video. The quality of the photo/video does not match the content of Arabsstock"],
        ];
        foreach ($data as $key => $value) {
            RejectionReason::create($value);
        }

        /* 
                $table->bigIncrements('id');
            $table->enum('type',['indeterminate','final']);
            $table->enum('status',['active','disabled'])->default('active');
            $table->string('title')->nullable();
            $table->text('descreption')->nullable();
            $table->softDeletes();
            $table->timestamps();
            ⭕في حال كانت الصور غير واضحة من ناحية المكان و الوصف (📍رفض غير نهائي)
            👈🏻سبب الرفض :
            عزيزي المساهم يرجى تزويدنا بمعلومات  اكثرحول الصوره / الفيديو ( عنوان مكان الصوره/ الفيديو + وصف خاص للصورة/ للفيديو)
            ⭕في حال ظهور أشخاص (📍رفض غير نهائي)
            👈🏻سبب الرفض :
            عزيزي المساهم نعتذر عن قبول قبول الصوره / الفيديو بسبب ظهورأشخاص ، يرجى إرفاق نموذج موافقة العارض على الظهور , يمكنك إعادة رفع النماذج وإعادة تقديمها مباشرة للمراجعة
            ⭕في حال كانت الصور مكررة (📍رفض نهائي)
            👈🏻سبب الرفض :
            عزيزي المساهم نعتذر عن  قبول الصوره ، الصور مكررة , لا يمكن رفع ذات الصور المتشابهة والمتكررة
            
            ⭕في حال ظهور عناوين وشعارات وغيره (📍رفض غير نهائي)
            👈🏻سبب الرفض :
            عزيزي المساهم نعتذرعن قبول الصوره / الفيديو، بسبب ظهور شعارات وأسماء تجارية تمتلك حقوق ملكية للنشر، يمكنك إعادة رفع النموذج وإعادة تقديمها مباشرة للمراجعة
            ⭕في حال كانت الصوره مائله ( 📍رفض غير نهائي )
            👈🏻سبب الرفض :
            عزيزي المساهم نعتذرعن قبول الصورة ، لعدم موافقتها مع شروط القبول لعربستوك : بسبب ان ابعاد و اتجاه الصوره  خاطئ ، بامكانك تعديل الابعاد و اعاده ارسال الصوره
            ⭕في حال وجود لوجو او توقيع في الصوره (📍رفض نهائي)
            👈🏻سبب الرفض :
            عزيزي المساهم نعتذر عن قبول الصوره / الفيديو ،  بسبب وجود لوجو شخصي مخالف لحقوق ملكية النشر
            ⭕في حال كانت الصوره لا تدل على دول الخليج(📍رفض نهائي)
            👈🏻سبب الرفض :
            عزيزي المساهم نعتذر عن قبول الصوره / الفيديو ، الصوره / الفيديو لا تتناسب مع شروط القبول الخاصه بعربستوك  ، يجب ان تكون الصوره / الفيديو لها اختصاص بالمحتوى الخليجي
            ⭕في حال كانت جودة الصوره سيئة (📍رفض نهائي)
            👈🏻سبب الرفض :
            عزيزي المساهم نعتذرعن قبول الصورة / الفيديو، جودة الصورة / الفيديو لا  تناسب محتوى عربستوك
        
        */

    }
}
