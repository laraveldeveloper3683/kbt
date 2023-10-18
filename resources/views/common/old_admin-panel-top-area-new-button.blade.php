<div class="row page-titles">
    <div class="col-md-5 align-self-center">
        <h4 class="text-themecolor">{{ucfirst(Request::segment(2))}}</h4>
    </div>
    <div class="col-md-7 align-self-center text-end">
        <div class="d-flex justify-content-end align-items-center">
            <ol class="breadcrumb justify-content-end">
              <li class="breadcrumb-item"><a href="/accountadmin">Home</a></li>
              <li class="breadcrumb-item active"><a href="/accountadmin/{{Request::segment(2)}}">{{ucfirst(Request::segment(2))}}</a></li>
            </ol>
            <a href="/accountadmin/{{Request::segment(2)}}/add"> <button type="button" class="btn btn-info d-none d-lg-block m-l-15 text-white"><i
                    class="fa fa-plus-circle"></i> Create New</button></a>
        </div>
    </div>
</div>
