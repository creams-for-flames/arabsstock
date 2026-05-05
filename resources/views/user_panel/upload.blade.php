@extends('app')

@section('css')
<style>
    body .dropzone .dz-preview {
    margin: 15px;}
    body .dropzone .dz-preview .dz-success-mark, body .dropzone .dz-preview .dz-error-mark {
    left: 25%;}
    body .dropzone {
    min-height: 150px;
    border: 2px solid rgb(221, 221, 221);
    background: white;
    padding: 4rem 20px;
}
    body .dropzone .dz-message .dz-button {
    font-size: 18px;
    color: #aaa;
}
.content-upload {
  padding: 50px 0;
}
.content-upload .title-upload h3 {
  color: #777;
  margin: 0 0 2rem 0;
}
.head-content {
  padding: 15px;
  border: 1px solid #b8d59f;
  margin: 0 0 2rem;
  background: #e8f2e0;
}
.dropzone {
  margin-bottom: 2rem;
}
.next {
  border: 2px solid #b2b2b2;
  margin: auto;
  margin-bottom: 2rem
}
.next {
  padding: 1rem
}
.next button {
  width: 120px;
  padding: 5px;
}

.uploading-content {
  margin-top: 4rem;
}
.uploading-content h3 {
  font-size: 18px;
  margin-bottom: 1rem;
  margin-top: 1rem;
}
.uploading-content .video-image,
.uploading-content .vectors,
.uploading-content .need-more {
  padding: 2rem;
}
.uploading-content .vectors {
  border-left: 1px solid #ebebeb;
  border-right: 1px solid #ebebeb;
}
</style>
@endsection

@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://rawgit.com/enyo/dropzone/master/dist/dropzone.js"></script>
<link rel="stylesheet" href="https://rawgit.com/enyo/dropzone/master/dist/dropzone.css">

<div class="content-upload">
  <div class="container">
      <div class="row"><div class="col-md-12">
    <div class="title-upload">
      <h3>رفع المحتوى</h3>
    </div>
    <div class="head-content">
      <p>تحميل ناقلات؟ الآن. ببساطة تحميل ملفات eps الخاص بك. لا يلزم jpeg.</p>
      <p>يتم الآن إنشاء معاينات JPEG تلقائيًا لملفات EPS. لم تعد بحاجة إلى تحميل ملف JPEG مع كل متجه</p>
    </div>


    <form id="my-dropzone" class="dropzone" enctype="multipart/form-data">
      <div class="dropzone-previews"></div>
      <div style="
    margin: 0 auto;
    text-align: center;
    position: relative;
    z-index: 9;
"><i class="fa fa-cloud-upload" style="
    margin: 0 auto;
    text-align: center;
    font-size: 60px;
    color: #ddd;
"></i>
</div>

      <div class="fallback">
        <input name="image" type="file" />
      </div>
    </form>

    <div class="next">
      <button type="next" class="btn btn-success">التالي</button>
    </div>
    <div class="uploading-content">
      <div class="row">
        <div class="col-md-4">
          <div class="video-image">
            <h3>حصلت على مقاطع فيديو أو الكثير من الصور؟</h3>
            <p>يرجى تحميل باستخدام FTP. <a href="#">المزيد</a></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="vectors">
            <h3>تحميل ناقلات؟</h3>
            <p>قم بتحميل ملفات EPS متوافقة مع الإصدار 8 أو 10. المصور لا يتطلب JPEG مطابق! <a href="#">المزيد</a></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="need-more">
            <h3>هل تريد المزيد من المساعدة؟</h3>
            <p>قم بزيارة <a href="مركز الدعم">مركز الدعم</a> للحصول على أدلة تفصيلية والأسئلة الشائعة</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
</div>



@endsection

@section('javascript')
<script>
'use strict'

Dropzone.options.myDropzone = {
    url: "upload.php",
    dictDefaultMessage: "اسحب الصور وافتها هنا للتحميل ",

    init: function() {
        var myDropzone = this;

        this.on("drop", function(event) {
          var imageUrl = event.dataTransfer.getData('URL');
          var fileName = imageUrl.split('/').pop();

          // set the effectAllowed for the drag item
          event.dataTransfer.effectAllowed = 'copy';

          function getDataUri(url, callback) {
            var image = new Image();

            image.onload = function() {
              var canvas = document.createElement('canvas');
              canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
              canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

              canvas.getContext('2d').drawImage(this, 0, 0);

              // Get raw image data
              // callback(canvas.toDataURL('image/png').replace(/^data:image\/(png|jpg);base64,/, ''));

              // ... or get as Data URI
              callback(canvas.toDataURL('image/jpeg'));
            };

            image.setAttribute('crossOrigin', 'anonymous');
            image.src = url;
          }

          function dataURItoBlob(dataURI) {
            var byteString,
                mimestring

            if (dataURI.split(',')[0].indexOf('base64') !== -1) {
              byteString = atob(dataURI.split(',')[1])
            } else {
              byteString = decodeURI(dataURI.split(',')[1])
            }

            mimestring = dataURI.split(',')[0].split(':')[1].split(';')[0]

            var content = new Array();
            for (var i = 0; i < byteString.length; i++) {
              content[i] = byteString.charCodeAt(i)
            }

            return new Blob([new Uint8Array(content)], {
              type: mimestring
            });
          }

          getDataUri(imageUrl, function(dataUri) {
            var blob = dataURItoBlob(dataUri);
            blob.name = fileName;
            myDropzone.addFile(blob);
          });
        });

    } // init
} // Dropzone
</script>

@endsection
