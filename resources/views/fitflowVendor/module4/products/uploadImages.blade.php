@extends('vendorLayouts.master')

@section('title')
 {{ $productName }}-Upload Images
@endsection

@section('css')
<!-- Imported styles on this page -->
<link rel="stylesheet" href="{{ asset('assets/js/dropzone/dropzone.css') }}">
<style>
    /***CUSTOM CSS FOR DROP ZONE***/
    div.table {
        display: table;
        margin-bottom: 0 !important;
        width:100%;
        border: 1px solid #dadada;
    }
    div.table .file-row {
        display: table-row;
    }
    div.table .file-row > div {
        display: table-cell;
        vertical-align: middle;
        border-bottom: 1px solid #ddd;
        padding: 8px;
    }
    div.table .file-row:nth-child(odd) {
        background: #f9f9f9;
    }
    .progress.progress-striped.active {
        margin-bottom: 0px;
        width: 150px;
    }
    div.table.table-striped.files .file-row:nth-child(1) .column{
        border-top: 1px solid #ddd;
    }
    div.table.table-striped.files.uploadedFiles .file-row .column:nth-child(4) {
        /* border-right: 1px solid #ddd; */
    }
    /*div.files .file-row .column:nth-child(1) {
        width: 10%;
        border-left: 1px solid #ddd;
    }*/
    div.files .file-row .column:nth-child(2) {
        width: 100%;
    }
    div.files .file-row .column:nth-child(3) {
        width: 15%;
    }
    div.files .file-row .column:nth-child(1) {
        width: 10%;
        border-left: 1px solid #ddd;
    }
    div.uploadedFiles .file-row .column:nth-child(5) {
        width: 7%;
        border-right: 1px solid #ddd !important;
    }
    div.previewFile .file-row .column:nth-child(6) {
        width: 7%;
        border-right: 1px solid #ddd;
    }
    /*div.uploadedFiles .file-row .column:nth-child(5) {
        width: 7%;
        border-right: 1px solid #ddd !important;
    }*/
    /*div.previewFile .file-row .column:nth-child(6) {
        width: 7%;
        border-right: 1px solid #ddd;
    }*/
    div.files .file-row .column p{
        margin: 0;
    }
    .dropzone {
        border: 2px dashed #24b092;
        border-radius: 3px;
        background: #f9f9f9;
        padding: 10px;
        margin-top: 5px;
        box-sizing: border-box;
        cursor: pointer;
    }
    .dropzone:hover {
        background: #f1f1f1;
    }
    .dropzone .dz-message{    
        text-align: center;
    }
    .dropzone-file-section {
        max-height: 315px;
        overflow-y: auto;
        width:100%;
    }
    .dropzone{min-height:250px !important;margin-bottom: 40px;}
    .dropzone .dz-default.dz-message{background:transparent;}
    .dropzone .dz-default.dz-message span {
        /* display: none; */
        margin-top: 40px;
        display: block;
        font-size: 20px;
    }
    /* Hide the progress bar when finished */
    .previewFile .file-row.dz-success .progress {
        opacity: 0;
        transition: opacity 0.3s linear;
    }
    .previewFile .file-row.dz-error .progress{
        opacity: 0;
    }
    .previewFile .file-row .delete {
        display: block;
    }
    .previewFile .file-row .cancel {
        display: none;
        cursor: pointer;
    }
    .previewFile .file-row.dz-success .delete {
        display: block;
    }
    .previewFile .file-row.dz-error .cancel {
        display: block;
    }
    .previewFile .file-row .view{
        display: none;
    }
    .previewFile .file-row.dz-success.dz-complete .view{
        display: block;
    }
    #files p{
        margin: 0;
    }
    .dropzone-file-section::-webkit-scrollbar-track
    {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        background-color: #F5F5F5;
    }
    .dropzone-file-section::-webkit-scrollbar
    {
        width: 6px;
        background-color: #F5F5F5;
    }
    .dropzone-file-section::-webkit-scrollbar-thumb
    {
        background-color: #24b092;
    }
    /***CUSTOM CSS FOR DROP ZONE***/
</style>
@endsection

@section('content')

@section('breadcrumb')
<li>

    <a href="{{ url($configM4.'/products') }}">Products</a>
</li>

@endsection

@section('pageheading')
{{ $productName }}
@endsection


<div class="row">

    <div class="col-md-12">
        @if(count($errors))
        @include('vendorLayouts.flash-message')
        @yield('form-error')
        @endif

        <div class="panel-body">

            <!--UPLOAD PROPOSAL-->
            <div class="row">
                <div class="col-md-6">

                    <div class="dropzone uploadProposal"></div>
                </div>
                  <div class="col-md-6">
                    <div class="dropzone-file-section mt-1">
                        <div class="table table-striped files uploadedFiles" style="margin-bottom: 0">
                            @foreach ($productImages as $productImage)
                            <div class="file-row f{{ $productImage->id }}" id="vendor_images">                                                       
                                <div class="column">
                                    <span class="preview">
                                        <img src="{{ url('public/products_images/'.$productImage->image) }}" style="width:40px;height:40px;">                                               
                                    </span>
                                </div>

                                <div class="column">

                                    <span class="btn btn-danger btn-xs pull-right delete_afterpageload" id="deleteFile" data-id="{{ $productImage->id }}" ><i class="fa fa-trash-o"></i> Delete</span>

                                </div>

                            </div>                                                
                            @endforeach
                        </div>


                        <div class="table table-striped files previewFile" id="proposalPreviews">                                                
                            <div id="proposalTemplate" class="file-row">
                                <!-- This is used as the file preview template -->
                                <div class="column">
                                    <span class="preview"><img data-dz-thumbnail /></span>
                                </div>

                                <div class="column">
                                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                        <div class="progress-bar progress-bar-success" data-dz-uploadprogress></div>
                                    </div>
                                </div>                                                      
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!--UPLOAD PROPOSAL ENDS HERE-->


        </div>



    </div>
</div>


@endsection

@section('scripts')
<!-- Imported scripts on this page -->
<script src="{{ asset('assets/js/fileinput.js') }}"></script>
<script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>
<script src="{{ asset('assets/js/toastr.js') }}"></script>

<script>
$(document).ready(function () {
    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#proposalTemplate");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    Dropzone.autoDiscover = false;

    var myDropzone = new Dropzone(".uploadProposal", {// Make the whole body a dropzone
        "url":  '{{ url("$configM4/products/$productID/images") }}',  // Set the url
        thumbnailWidth: 40,
        thumbnailHeight: 40,
        parallelUploads: 1,
        previewTemplate: previewTemplate,
        autoQueue: true, // Make sure the files aren't queued until manually added
        previewsContainer: "#proposalPreviews", // Define the container to display the previews
        clickable: ".uploadProposal", // Define the element that should be used as click trigger to select files.
        acceptedFiles: "image/*",
        filesizeBase: 1024,
        params: {
            _token: '{{ csrf_token() }}'
        },
        accept: function (file, done) {
            if (file.size > 1048576) {
                done('File size exceeded limit of 1 MB');
            } else {
                done();
            }
        },
        init: function () {
            this.on("addedfile", function (file) {
                if (file.size > 1048576) {
                    var removeButton = Dropzone.createElement("<div class='column'><span class='btn btn-danger btn-xs pull-right cancel'><i class='fa fa-times'></i></span></div>");
                } else {
                    var removeButton = Dropzone.createElement("<div class='column'><span data-dz-remove class='btn btn-danger btn-xs pull-right delete'><i class='fa fa-trash-o'></i> Delete</span></div>");
                }
                file.previewElement.appendChild(removeButton);

//                    var viewButton = Dropzone.createElement("<div class='column'><span class='btn btn-primary btn-xs pull-right view'><i class='fa fa-eye'></i></span></div>");
//                    file.previewElement.appendChild(viewButton);

                // Capture the Dropzone instance as closure.
                var self = this;

                // Listen to the click event
                removeButton.addEventListener("click", function (e) {
                    if (file.size > 1048576) {
                        self.removeFile(file);
                    } else if (window.confirm("{{ config('global.deleteConfirmation') }}")) {
                        $.ajax({
                            type: "POST",
                            async: true,
                            "url":  '{{ url("$configM4/products/deleteImage") }}/' + file.id, 
                            data: {id: file.id, _token: '{{ csrf_token() }}'},
                            success: function (data) {
                                self.removeFile(file);
                            }
                        });
                    }
                });

                // Listen to the view event
//                    viewButton.addEventListener("click", function (e) {
//                        window.open(file.viewLink, '_blank');
//                    });

                // CUSTOM THUMBNAIL FOR PDF FLES
                if (file.type == "application/pdf") {
                    file.previewElement.querySelector("[data-dz-thumbnail]").src = "assets/img/pdf-icon.png";
                }
            });
        }
    });

    myDropzone.on("processing", function (file) {
        $("#save").attr("disabled", true);
        $("#submit").attr("disabled", true);
    });

    myDropzone.on("success", function (file, response) {
        //console.log(response.id);
        if (response.error) {
            toastr.success(response.error, "", opts);
        }
        if (response.id) {
            file.id = response.id;
        }


    });



    myDropzone.on("queuecomplete", function (file) {
        $("#save").attr("disabled", false);
        $("#submit").attr("disabled", false);
    });

});// Document ready function ends here


/*----Delete Update---*/
$(document).on('click', '.delete_afterpageload', function (e) {
    if (confirm('{{ config('global.deleteConfirmation') }}')) {
        var ID = $(this).attr('data-id');

        $.ajax({
            type: "POST",
            async: true,
            "url":  '{{ url("$configM4/products/deleteImage") }}/' + ID, 
            data: {id: ID, _token: '{{ csrf_token() }}'},
            success: function (data) {
                $('#vendor_images.f' + data.id).css('display', 'none');

                toastr.success(data.response, "", opts);
                // Sample Toastr Notification
                var opts = {
                    "closeButton": true,
                    "debug": false,
                    "positionClass": rtl() || public_vars.$pageContainer.hasClass('right-sidebar') ? "toast-top-left" : "toast-top-right",
                    "toastClass": "sucess",
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                /*------END----*/
            }
        });
    } else {
        return false;
    }

});

</script>
@endsection