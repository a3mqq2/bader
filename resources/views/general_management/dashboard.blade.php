@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="">
    <div class="row g-3">

      <div class="col-md-12">

         <div class="col-md-12">
            <div class="card welcome-banner" style="background:#063973 !important">
               <div class="card-body">
                 <div class="row">
                   <div class="col-sm-6">
                     <div class="p-4">
                       <h2 class="text-white fw-bold">مرحبًا بك في لوحة التحكم</h2>
                       <p class="text-white mb-4">
                         يمكنك من خلال هذه الصفحة الاطّلاع بسرعة على إحصائيات المستخدمين
                         بالإضافة إلى إدارة المستخدمين.
                       </p>
                     </div>
                   </div>
                   <div class="col-sm-6 text-center">
                     <div class="img-welcome-banner">
                       <img src="../assets/images/widget/welcome-banner.png" alt="لوحة التحكم" class="img-fluid">
                     </div>
                   </div>
                 </div>
               </div>
             </div>
         </div>

      </div>

    </div>
</div>
@endsection

@push('styles')
<style>
.stat-card {
    transition: transform .15s ease, box-shadow .15s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 18px rgba(0,0,0,.08);
}
.icon-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.soft-success { background: #e9f9ef; color: #28a745; }
.soft-danger { background: #fdeaea; color: #dc3545; }
.fs-2 { font-size: 1.8rem !important; }
</style>
@endpush
