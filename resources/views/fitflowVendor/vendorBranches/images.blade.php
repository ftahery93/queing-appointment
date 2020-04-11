@foreach ($images as $image)
 <div class="file-row">                                                       
                                <div class="column">
                                    <span class="preview">
                                        <img src="{{ url('public/vendors_images/'.$image->image) }}" style="width:70px;height:70px;">                                               
                                    </span>
                                </div>
                                                                                   
                                <div class="column">

                                    <span class="btn btn-danger btn-xs pull-right delete_afterpageload" id="deleteFile" data-id="{{ $image->id }}" ><i class="fa fa-trash-o"></i> Delete</span>

                                </div>
                               
                            </div>         
@endforeach
