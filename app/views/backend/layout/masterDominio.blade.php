@include('backend.layout.header')
@include('backend.layout.topBar')
@include('backend.layout.leftBar')
<div class="mainpanel">
    <div class="pageheader">
        <div class="media">
            <div class="pageicon pull-left">
                <i class="fa fa-home"></i>
            </div>
            <div class="media-body">
            {{SistemaFunciones::breadCumbBackend(Request::segment(3))}}
            <!--
                <ul class="breadcrumb">
                    <li><a href="{{URL::to('/admingm')}}"><i class="glyphicon glyphicon-home"></i></a></li>
                    <li><a href="#">Pages</a></li>
                    <li>Blank Page</li>
                </ul>
                <h4>Blank Page</h4>
                -->
            </div>
        </div><!-- media -->
    </div><!-- pageheader -->
    <div class="contentpanel">
        @yield('contenido')
    </div><!-- contentpanel -->
</div>
@include('backend.layout.scripts')
@include('backend.layout.footer')