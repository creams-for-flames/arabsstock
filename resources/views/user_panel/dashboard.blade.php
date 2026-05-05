@extends('app')

@section('css')
    <style>
    .user-panel {
      padding: 80px 0;
      background-color: #eee;
    }
    .user-panel .btn-userpanel {
      margin-bottom: 1rem;
      width: 100%;
      padding: 10px;
      border-radius: 3px;
    }
    .user-panel .list-group {
      border-radius: 3px;    margin-bottom: 0;
    }
    .user-panel .list-group .list-group-item {
      padding: 15px 0px;
      border: 0;
      margin-bottom: 0px;
    }
    .user-panel .list-group .list-group-item .badge {
        border-radius: 15px;
    padding: 3px 10px;
    width: auto;
    height: auto;
    line-height: 16px;
    background-color: #eeeeee;
    color: #333;
    }
    .user-panel .block-user ul {
      padding-right: 0;
    }
    .user-panel .block-user ul li {
      padding: 15px;
      margin-bottom: 2rem;
      border-radius: 3px;
    }
    .user-panel .block-user .red-list {
      background-color: #faeeee;
      border: 1px solid #edbbbc;
    }
    .user-panel .block-user .white-list {
      background-color: #ffffff;
      border: 1px solid #cccccc;
    }
    .user-panel .section-user {
      background: #fff;
      border-radius: 3px;
      padding: 15px;
      margin-bottom: 2rem;
    }
    .section-user p.title {
      font-size: 16px;
      color: #777;
    }
    .section-user .unpaid-eamings {
    }
    .section-user .unpaid-eamings .number-dolar {
      margin: 0 0 1rem 0;
    }
    .section-user .unpaid-eamings .bottom-doler {

    }
    .section-user .left-sectionuser .pargraph-earnings {
    }
    .section-user .left-sectionuser a {
      color: #31c0a2;
    }
    .section-user .left-sectionuser .pargraph-earnings,
    .section-user .left-sectionuser a {
      border-right: 1px solid #eee;
      padding: 0 10px;
    }
    .flex-select {
    }
    .flex-select select {
      margin: 0 5px;
    }
    .flex-select .small {
    }
    .flex-select big {
    }
    .content-user .title-selctor p {
      margin: 5px 0;

    }
    .content-user div.chart-salary img {
    width: 100%;
}
    .content-user .top1 {
      margin-top: 2rem;
      margin-bottom: 7rem;
    }
    .content-user .bootom0 p {
      margin-bottom: 0;
    }
    .content-user .bootom0 span {
      padding: 0 35px;
      font-size: 11px;
    }
    .content-user .bootom0 span.left-bootom0 {
      float: left;
    }
    .content-user p:after {
      content: "";
      display: inline-block;
      width: 97%;
      background-color: #eee;
      height: 2px;
      margin-right: 10px;
    }
    .section-user .total-earnings .bottom-doler,
    .section-user .total-downloads .bottom-doler {
      font-size: 12px;
    }
    .section-user .total-earnings {
      margin-bottom: 3rem;    margin-top: 4rem;
    }
    .section-user .total-downloads {
      margin-bottom: 7rem;
    }
    .section-user .view-datailed {
      color: #31c0a2;
    }
    .earnings-types {
      padding: 6rem 0 1rem;
    }

    .earnings-types p {
      color: #31c0a2;
      margin-bottom: 2rem !important;
    }
    .earnings-types .earnings {
      margin-bottom: 15px;
      display: flex;
      font-size: 12px;
    }
    .earnings-types .earnings .title-earnings {
      width: 25%;
      font-size: 13px;
    }
    .earnings-types .earnings .num-earnings {
      text-align: left;
      width: 15%;
      font-size: 13px;
    }
    .earnings-types .earnings .progress{
        margin-bottom: 0;
    width: 60%;
    margin: 6px 20px 0;
    height: 10px;
    box-shadow: unset;
    }
    .sets-performers .sets,
    .sets-performers .performers {
      background: #fff;
      border-radius: 3px;
      padding: 15px;
      margin-bottom: 2rem;
    }
    .sets-performers .sets .title,
    .sets-performers .performers .title {
      margin-top: 5px;
    }
    .sets-performers .sets .sets-track,
    .sets-performers .performers .looks-like {
      padding: 4rem 7rem 3rem;
    }
    .sets-performers .sets .sets-track h3,
    .sets-performers .performers .looks-like h3 {
      margin-bottom: 2rem;
      font-size: 18px;
      color: #777;
      line-height: 1.6;
    }
    .sets-performers .sets .sets-track p,
    .sets-performers .performers .looks-like p {
      font-size: 14px;
      margin-bottom: 2rem;
      line-height: 1.6;
    }
    </style>
@endsection

@section('content')

<div class="user-panel">
  <div class="container">
    <div class="row">

        <div class="col-md-9">
          <div class="block-user">
              <div data-reactroot="">
                  <div class="alert message alert-danger ">
                      <div>يرجى تزويدنا بتفاصيل العائد الخاصة بك حتى نتمكن من الدفع لك بمجرد تراكم الأرباح.</div>
                    </div>
                    <div class="alert message alert-warning ">
                        <div>استمر في المحاولة! لم يتم قبول طلبك. راجع أسباب رفض الصورة أو الفيديو ، ثم حمِّل المزيد من أفضل أعمالك.</div></div></div>

          </div>

          <div class="section-user">
            <div class="row">
              <div class="col-md-6">
                <div class="unpaid-eamings">
                  <p class="title">أرباح غير مدفوعة</p>
                  <h2 class="number-dolar">$28.01</h2>
                </div>
              </div>
              <div class="col-md-6">
                <div class="left-sectionuser">
                  <p class="pargraph-earnings">يتم حساب الدفعات في نهاية كل شهر للمساهمين الذين يستوفون الحد الأدنى لمبلغ العائد.</p>
                </div>
              </div>
              </div>

              <div class="row">
              <div class="col-md-6">
                <div class="unpaid-eamings">
                  <span class="bottom-doler">يتم تحديثه كل 15 دقيقة تقريبًا*</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="left-sectionuser">
                  <a href="#">المزيد</a>
                </div>
              </div>
            </div>
          </div>

          <div class="section-user">
            <div class="title-selctor">
              <div class="row">
                <div class="col-md-8">
                  <p class="title">الدخل الشهري</p>
                </div>
                <div class="col-md-4">

                    <div class="row flex-select">
                    <select class="small col-md-5 ">
                      <option value="volvo">الجميع</option>
                    </select>
                    <select class="big col-md-6 ">
                      <option value="volvo">12 شهرًا الماضية</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="content-user">
              <div class="chart-salary">
                  <img src="{{ asset('img/chart.png') }}">
              </div>

            </div>
          </div>

          <div class="section-user">
            <div class="row">
              <div class="col-md-6">
                <div class="earnings-summary">
                  <div class="row">
                    <div class="col-md-6">
                      <p class="summary title">ملخص الأرباح</p>
                    </div>
                    <div class="col-md-6">
                        <div class="row flex-select">
                    <select class="small col-md-5 ">
                      <option value="volvo">الجميع</option>
                    </select>
                    <select class="big col-md-6 ">
                      <option value="volvo">جميع الأوقات  </option>
                    </select>
                  </div>

                    </div>
                  </div>
                </div>
                <div class="total-earnings">
                  <h2 class="number-dolar">$137.00</h2>
                  <span class="bottom-doler">الأرباح الكلية</span>
                </div>
                <div class="total-downloads">
                  <h2 class="number-dolar">0.60</h2>
                  <span class="bottom-doler">إجمالي التنزيلات</span>
                </div>
                <a href="#" class="view-datailed">عرض ملخص الكسب التفصيلي</a>
              </div>

              <div class="col-md-6">
                <div class="earnings-types">
                  <p class="title">أنواع الأرباح</p>
                  <div class="earnings">
                    <span class="title-earnings">سلة التسوق</span>
                    <div class="progress">
                      <div class="progress-bar progress-market" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="num-earnings">$16.00</span>
                  </div>
                  <div class="earnings">
                    <span class="title-earnings">حزم مقطع</span>
                    <div class="progress">
                      <div class="progress-bar progress-bakage" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="num-earnings">$24.00</span>
                  </div>
                  <div class="earnings">
                    <span class="title-earnings">المحسن</span>
                    <div class="progress">
                      <div class="progress-bar progress-best" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="num-earnings">$31.00</span>
                  </div>
                  <div class="earnings">
                    <span class="title-earnings">عند الطلب</span>
                    <div class="progress">
                      <div class="progress-bar progress-demand" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="num-earnings">$11.00</span>
                  </div>
                  <div class="earnings">
                    <span class="title-earnings">الدعوات</span>
                    <div class="progress">
                      <div class="progress-bar progress-invite" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="num-earnings">$9.00</span>
                  </div>
                  <div class="earnings">
                    <span class="title-earnings ">واحد والآخر</span>
                    <div class="progress ">
                      <div class="progress-bar progress-other" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="num-earnings">$18.00</span>
                  </div>
                  <div class="earnings">
                    <span class="title-earnings ">الاشتراكات</span>
                    <div class="progress">
                      <div class="progress-bar progress-participation" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="num-earnings">$28.00</span>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="sets-performers">
            <div class="row">
              <div class="col-md-6">
                <div class="sets">
                  <div class="row">
                    <div class="col-md-6">
                      <p class="title">مجموعات</p>
                    </div>
                    <div class="col-md-6">
                      <div class="flex-sets text-left">
                        <button class="btn btn-success">تحديد المجموعات</button>
                      </div>
                    </div>
                  </div>
                  <div class="sets-track text-center">
                    <h3>إنشاء مجموعات الصور والفيديو لتتبع أرباحك.</h3>
                    <p>عرض أداء المحتوى الخاص بك والبقاء منظمًا مع المجموعات.</p>
                    <button class="btn btn-success">إنشاء مجموعة</button>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="performers">
                  <div class="row">
                    <div class="col-md-6">
                      <p class="title">أفضل 5 مؤدين</p>
                    </div>
                    <div class="col-md-6">
                         <div class="row flex-select">
                    <select class="small col-md-5 ">
                      <option value="volvo">الجميع</option>
                    </select>
                    <select class="big col-md-6 ">
                      <option value="volvo">جميع الأوقات  </option>
                    </select>
                  </div>

                    </div>
                  </div>

                  <div class="looks-like text-center">
                    <h3>يبدو أنه ليس لديك أي مبيعات حتى الآن.</h3>
                    <p>استمر في التحميل! تزيد المحفظة الأكبر من فرصك في البيع.</p>
                    <button class="btn btn-success">تحميل المحتوى</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
         <div class="col-md-3">

          <div class="tab-img-video">

          <nav>
  <ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item active">
      <a class="nav-item nav-link " id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">الصور</a>
  </li>
  <li class="nav-item">
    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">الفيديو</a>
  </li>
  </ul>
</nav>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade in show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                <a href="upload" class=" btn btn-success btn-userpanel btn btn-success btn-lg btn-block">
            تحميل صور <i class="fa fa-cloud-upload">
                            </i>
          </a>
       <ul class="list-group">
            <li class="list-group-item"><span class="badge">2020-02-16</span> تاريخ النشر</li>
            <li class="list-group-item"><span class="badge">png</span> نوع الصورة</li>
            <li class="list-group-item"><span class="badge">5210px</span> الدقة</li>
            <li class="list-group-item"><span class="badge">مجتمع</span> الفئة</li>
            <li class="list-group-item"><span class="badge">15kp</span> حجم الملف</li>
          </ul>
          </div>
  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <a href="upload" class=" btn btn-success btn-userpanel btn btn-success btn-lg btn-block">
            تحميل فيديوهات <i class="fa fa-cloud-upload">
                            </i>
          </a>
       <ul class="list-group">

            <li class="list-group-item"><span class="badge">2020-02-16</span> تاريخ النشر</li>
            <li class="list-group-item"><span class="badge">MB4</span> نوع الفيديو</li>
            <li class="list-group-item"><span class="badge">4k</span> الدقة</li>
            <li class="list-group-item"><span class="badge">علوم</span> الفئة</li>
            <li class="list-group-item"><span class="badge">1.2MB</span> حجم الملف</li>
          </ul>
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
$('#myTab a').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})
</script>

@endsection
