{@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--tabs">
            <div class="kt-portlet__body">

                <div id="app">
                    <div class="container">
                        <div class="errors">

                        </div>
                        <form id="sessiion" method="POST"  enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ trans('admin.name') }}:</label>
                                    <input type="text" id="folder" name="folder" value="{{ $data->folder }}" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>{{ trans('admin.created_session') }}:</label>
                                    <input type="text" id="created_at_picker" name="session_date" value="{{ $data->session_date }}" class="form-control">
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <label>{{ trans('admin.photographers') }}:</label>
                                    <button type="button" id="addPhotographerBtn"
                                        class="btn btn-primary m-2">{{ trans('admin.add') }}
                                        {{ trans('admin.photographers') }}</button>

                                    <div id="photographersContainer">
                                        @if (isset($data) &&  isset($data->photographers) && count($data->photographers))
                                            @foreach ($data->photographers as $item)

                                            <div class="form-group row photographer_raw">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" value="{{$item->name}}" name="photographers[{{$loop->index	}}][name]" placeholder="{{ trans('admin.name') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control"  value="{{$item->id_number}}"  name="photographers[{{$loop->index	}}][id_number]" placeholder="{{ trans('admin.id_number') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" value="{{$item->email}}" name="photographers[{{$loop->index	}}][email]" placeholder="{{ trans('admin.email') }}">
                                                </div>
                                                <div class="col-md-1">
                                                    <object data="{{ cdn($item->pivot->contract_file) }}" type="application/pdf" width="100%" height="200px">
                                                        <p>Your browser does not support embedded PDFs. <a href="{{ cdn($item->pivot->contract_file) }}">Download the PDF</a> instead.</p>
                                                    </object>
                                                </div>
                                                <div class="d-none">
                                                    <textarea class="d-none" name="photographers[{{$loop->index	}}][contract]">{{ $item->pivot->contract }}</textarea>
                                                </div>
                                                <div class="col-md-2">
                                                <button type="button" class="btn btn-primary contractBtn" data-parent_elm="photographer_raw" data-toggle="modal">{{ __('admin.contract') }}</button>

                                                    <button type="button" class="btn btn-danger removePhotographerBtn">{{ trans('admin.delete') }}</button>
                                                </div>
                                            </div>
                                                
                                            @endforeach
                                        @endif
                                        <!-- Photographer template -->
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <label>{{ trans('admin.actors') }}:</label>
                                    <button type="button" id="addActorBtn"
                                        class="btn btn-primary m-2">{{ trans('admin.add') }}
                                        {{ trans('admin.actors') }}</button>

                                    <div id="actorsContainer">
                                        @if (isset($data) &&  isset($data->actors) && count($data->actors))
                                            @foreach ($data->actors as $item)
                                            <div class="form-group row actor_raw">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" value="{{$item->name}}" name="actors[{{$loop->index}}][name]" placeholder="{{ trans('admin.name') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" value="{{$item->id_number}}" name="actors[{{$loop->index}}][id_number]" placeholder="{{ trans('admin.id_number') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" value="{{$item->email}}" name="actors[{{$loop->index}}][email]" placeholder="{{ trans('admin.email') }}">
                                                </div>
                                                <div class="d-none">
                                                <textarea class="d-none" name="actors[{{$loop->index}}][contract]"> {{ $item->pivot->contract }}</textarea>
                                                </div>
                                                <div class="col-md-1">
                                                    <object data="{{ cdn($item->pivot->contract_file) }}" type="application/pdf" width="100%" height="600px">
                                                        <p>Your browser does not support embedded PDFs. <a href="{{ cdn($item->pivot->contract_file) }}">Download the PDF</a> instead.</p>
                                                    </object>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-primary contractBtn" data-parent_elm="actor_raw" data-toggle="modal">{{ __('admin.contract') }}</button>
                            
                                                    <button type="button" class="btn btn-danger removeActorBtn">{{ trans('admin.delete') }}</button>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif

                                        <!-- Actor template -->
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ trans('admin.country') }}:</label>
                                    <select name="country_id" id="country" class="form-control">
                                        @foreach ($countries as $country)
                                            <option 
                                            @if(isset($data) && $data->country_id == $country->id) selected
                                            
                                            @endif

                                                value="{{ $country->id }}"> {{ $country->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>{{ trans('admin.city') }}:</label>
                                    <select name="city_id" id="city" class="form-control">
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                @if (isset($data->city_id) && $data->city_id == $city->id)
                                                    slected="selected"
                                                @endif
                                                >{{ $city->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div id="locationContainer">
                                {{-- @foreach ($data->locations as  $item) --}}
                                    <div class="row location">
                                        <input type="hidden" id="location_id" value="{{$data->locations[0]->id??''}}"  name="location_id" class="form-control">

                                        <div class="col-12 mt-4 mb-4">{{ __('admin.location') }}</div>
                                        <div class="col-md-4">
                                            <label>{{ trans('admin.name') }}:</label>
                                            <input type="text" id="location_name"  value="{{$data->locations[0]->name??''}}" name="location_name" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label>{{ trans('admin.location_admin') }}:</label>
                                            <input type="text" id="location_admin"  value="{{$data->locations[0]->admin??''}}" name="location_admin"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label>{{ trans('admin.email') }}:</label>
                                            <input type="text" id="location_email"  value="{{$data->locations[0]->email??''}}" name="location_email"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label>{{ trans('admin.mobile') }}:</label>
                                            <input type="text" id="location_mobile"  value="{{$data->locations[0]->mobile??''}}" name="location_mobile"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label>{{ trans('admin.location_license') }}:</label>
                                            <input type="text" id="license_code"  value="{{$data->locations[0]->license_code??''}}" name="license_code"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label>{{ trans('admin.location') }}:</label>
                                            <input type="text" id="location"  value="{{$data->locations[0]->location??''}}" name="location" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="d-block">{{ trans('admin.contract') }}:</label>

                                            <button type="button" class="btn btn-primary w-100 contractBtn"
                                                data-parent_elm="location"
                                                data-toggle="modal">{{ __('admin.contract') }}</button>
                                            <textarea class="d-none" name="contract"> {{$data->locations[0]->pivot->contract??''}}</textarea>

                                        </div>
                                    </div>
                                
                                {{-- @endforeach --}}
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <label>{{ trans('admin.invoices') }}:</label>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>{{ trans('admin.invoices') }}:</label>
                                    <button type="button" id="addInvoiceBtn"
                                        class="btn btn-primary m-2">{{ trans('admin.add') }}
                                        {{ trans('admin.invoices') }}</button>

                                    <div id="invoicesContainer">
                                        <!-- invoicesContainer template -->
                                        @if (isset($data) &&  isset($data->invoices) && count($data->invoices))
                                            @foreach ($data->invoices as $item)
                                            <input type="hidden" name="invoices[{{$loop->index}}][id]" value="{{$item->id}}">
                                            <div class="form-group row invoice_raw">
                                                <div class="col-md-3">
                                                    <input type="text" class="form-control" value="{{$item->name}}" name="invoices[{{$loop->index}}][name]" placeholder="{{ trans('admin.name') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" min="1" class="form-control" value="{{$item->cost}}" name="invoices[{{$loop->index}}][cost]" placeholder="{{ trans('admin.cost') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="file" accept=".jpeg, .jpg, .png" name="invoices[{{$loop->index}}][file]" class="form-control invoiceFileInput">
                                                </div>
                                                <div class="col-1">
                                                    <img src="$item->file"  alt="Invoice Photo" class="rounded-circle w-100 h-50 invoicePhoto" >
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger removeInvoiceBtn">{{ trans('admin.delete') }}</button>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>{{ trans('admin.notes') }}:</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"  >{{$data->notes}}</textarea>

                                </div>
                            </div>

                            <button type="submit" class="btn btn-success mt-5 w-100">{{ __('views.Save') }}</button>
                        </form>
                    </div>
                    <!-- Large modal -->

                    <div class="modal fade bd-example-modal-lg" id="contract" tabindex="-1" role="dialog"
                        aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="form-group w-100">
                                    <label class="col-sm-2 control-label">{{ trans('admin.content_ar') }}</label>
                                    <div class="col-sm-12 border p-2">

                                        <textarea name="content_ar" id="content_ar" class="editable  " placeholder="{{ trans('admin.content_ar') }}"></textarea>
                                    </div>
                                </div>
                                <div class="form-group p-5 w-100">
                                    <label class="col-sm-12 control-label">{{ trans('admin.add_signature') }}</label>
                                    <div class="col-12">

                                        <canvas id="signatureCanvas" width="400" height="200"
                                            class="border"></canvas>
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-danger" id="clearButton">Clear
                                            Signature</button>

                                        <button type="button" class="btn btn-info" id="displayButton">Display
                                            Signature</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!-- end:: Content -->
    @endsection



    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.3/dist/flatpickr.min.css" rel="stylesheet">
        <style>
            /* Adjust the width of Summernote editor inside the modal */
            .modal-content .note-editor {
                width: 100%;
            }

            /* Optional: Adjust the width of Summernote toolbar to match the editor */
            .modal-content .note-toolbar {
                width: 100%;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.3/dist/flatpickr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

        <script>
            var photographerIndex = "{{count($data->photographers)}}";
            var actorIndex = "{{count($data->actors)}}";
            var invoiceIndex = "{{count($data->invoices)}}";
            var editorWidth, editorHeight;
            var contract_content = {!! json_encode($contract->content_ar??"محرر عقود اكتب نص العقد هنا ...") !!};
            var KTSummernoteDemo = function() {
                // Private functions
                var demos = function() {
                    $('.editable').summernote({
                        dialogsInBody: true,
                        airMode: true,
                        callbacks: {
                            // Callback function to calculate editor width and height after initialization
                            onInit: function() {
                                editorWidth = $('#summernote').width();
                                editorHeight = $('#summernote').height();
                            }
                        }

                    });
                }

                return {
                    // public functions
                    init: function() {
                        demos();
                    }
                };
            }();
            $(document).ready(function() {
                // <form id="sessiion" action="{{ route('admin.sessions.store', $type) }}" method="POST">

                $('#sessiion').submit(function(e) {
                    e.preventDefault();
                    var formData = new FormData(this); // Create a FormData object to send file and other form data

                    $.ajax({
                        type: 'POST',
                        url: "{{ $update_url }}",
                        processData: false, // Prevent jQuery from automatically processing the data
                        contentType: false,
                        data: formData,
                        beforeSend: function(xhr) {
                            $(".errors").html("");
                        },
                        success: function(response) {
                            if (response.success) {
                                swal.fire("", response.message, "success");
                                window.location.href = response.redirect;
                            }
                        },
                        error: function(xhr) {
                            var errors = xhr.responseJSON.errors;
                            $('html, body').animate({
                                scrollTop: 0
                            }, 'slow');
                            console.log("errors");
                            console.log(errors);
                            console.log(xhr);
                            var errs = "";
                            for (const key in errors) {
                                var err = `
                                <div class="alert alert-solid-danger alert-bold fade show " role="alert">
                                                                                <div class="alert-icon"><i class="fa fa-exclamation-triangle"></i>
                                                                                </div>
                                                                                <div class="alert-text">{{ __('views.Oops, something went wrong! Please check the errors below.') }}
                                                                                    ${key}:   ${errors[key][0]}
                                                                                </div>
                                                                                <div class="alert-close">
                                                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                                        <span aria-hidden="true"><i class="la la-close"></i></span>
                                                                                    </button>
                                                                                </div>
                                </div>                       
                                
                                `;
                                errs += err;
                            }


                            $(".errors").html(errs);
                        }
                    });
                });

                /* s:signture */
                var canvas = document.getElementById('signatureCanvas');
                var ctx = canvas.getContext('2d');
                var isDrawing = false;
                var lastX = 0;
                var lastY = 0;
                var signatureColor = '#0158ff'; // Default color is black

                function startDrawing(e) {
                    isDrawing = true;
                    [lastX, lastY] = [e.offsetX, e.offsetY];
                }

                function draw(e) {
                    if (!isDrawing) return;
                    ctx.beginPath();
                    ctx.moveTo(lastX, lastY);
                    ctx.lineTo(e.offsetX, e.offsetY);
                    ctx.strokeStyle = signatureColor; // Set the stroke color
                    ctx.stroke();
                    [lastX, lastY] = [e.offsetX, e.offsetY];
                }

                function stopDrawing() {
                    isDrawing = false;
                }

                function clearCanvas() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    $('#signatureImage').attr('src', '');
                }

                function displaySignature() {
                    var dataURL = canvas.toDataURL(); // Convert canvas data to data URL
                    var currentContent = $('.editable').summernote('code');
                    // Append custom HTML code at the current cursor position
                    var customHTML = `<div class="custom-box" >
                   
                        <table class="table"style="border:unset;">
                            <tbody>
                                    <tr>
                                        <td style="position: relative;border-top:unset;"><img src="${dataURL}" /></td>
                                        <td style="position: relative;border-top:unset;"></td>
                                    </tr>
                                </tbody></table>
                    
                    </div>`;
                    var newContent = currentContent + customHTML;

                    // Set the new content in the editor
                    $('.editable').summernote('code', newContent);
                    var active_contract = $('.modal-content').data('active_contract');
                    console.log(active_contract, 'active_contract');
                    var summernoteContent = getAllContent();
                    $(`${active_contract} textarea`).val(summernoteContent);

                    clearCanvas();
                }

                // Event listeners for drawing
                canvas.addEventListener('mousedown', startDrawing);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', stopDrawing);
                canvas.addEventListener('mouseout', stopDrawing);

                // Event listener to clear the canvas
                $('#clearButton').on('click', clearCanvas);

                // Event listener to display the signature as an image
                $('#displayButton').on('click', displaySignature);
                /* e:signture */
                $(document).on('click', '.contractBtn', function() {
                    var className = $(this).data('parent_elm');
                    const index = $(this).closest(`.${className}`).index();
                    var elm = `.${className}:eq(${index})`;
                    console.log(index, elm, $(`${elm} textarea`).val());
                    $('.modal-content').data('active_contract', elm);
                    var current_contract = $(`${elm} textarea`).val() ? $(`${elm} textarea`).val() :
                        contract_content;


                    $('.editable').summernote('code', current_contract);
                    $('.bd-example-modal-lg').modal('show');





                });

                $('#contract').on('shown.bs.modal', function() {
                    KTSummernoteDemo.init();


                });
                $('#addSignatureButton').on('click', function() {
                    displaySignature();
                });

                // Initialize datepicker for "Created At" field
                const created_at_picker = flatpickr("#created_at_picker", {
                    enableTime: false,
                    dateFormat: "Y-m-d",
                    defaultDate: new Date().toISOString().slice(0, 10), // Set default to current date

                    onClose: function(selectedDates, dateStr, instance) {
                        instance.close();
                    }
                });

                // Add Photographer
                $("#addPhotographerBtn").on('click', function() {
                    const photographerTemplate = `
                                                    <div class="form-group row photographer_raw">
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="photographers[${photographerIndex}][name]" placeholder="{{ trans('admin.name') }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="photographers[${photographerIndex}][id_number]" placeholder="{{ trans('admin.id_number') }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" name="photographers[${photographerIndex}][email]" placeholder="{{ trans('admin.email') }}">
                                                        </div>

                                                        <div class="d-none">
                                                            <textarea class="d-none" name="photographers[${photographerIndex}][contract]"></textarea>
                                                        </div>
                                                        <div class="col-md-2">
                                                        <button type="button" class="btn btn-primary contractBtn" data-parent_elm="photographer_raw" data-toggle="modal">{{ __('admin.contract') }}</button>

                                                            <button type="button" class="btn btn-danger removePhotographerBtn">{{ trans('admin.delete') }}</button>
                                                        </div>
                                                    </div>
                                            `;

                    $("#photographersContainer").append(photographerTemplate);
                    photographerIndex++; // Increment the index

                });

                // Remove Photographer
                $(document).on('click', '.removePhotographerBtn', function() {
                    $(this).closest('.photographer_raw').remove();
                    photographerIndex--; // Decrement the index when removing

                });

                // Add Actor
                $('#addActorBtn').on('click', function() {
                    const actorTemplate = `
                <div class="form-group row actor_raw">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="actors[${actorIndex}][name]" placeholder="{{ trans('admin.name') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="actors[${actorIndex}][id_number]" placeholder="{{ trans('admin.id_number') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="actors[${actorIndex}][email]" placeholder="{{ trans('admin.email') }}">
                    </div>
                    <div class="col-md-2">
                                <input type="file" accept=".jpeg, .jpg, .png" name="actors[${actorIndex}][file]" class="form-control invoiceFileInput">
                            </div>
                    <div class="d-none">
                    <textarea class="d-none" name="actors[${actorIndex}][contract]"></textarea>
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary contractBtn" data-parent_elm="actor_raw" data-toggle="modal">{{ __('admin.contract') }}</button>

                        <button type="button" class="btn btn-danger removeActorBtn">{{ trans('admin.delete') }}</button>
                    </div>
                </div>
            `;

                    $("#actorsContainer").append(actorTemplate);
                    actorIndex++;
                });

                // Remove Actor
                $(document).on('click', '.removeActorBtn', function() {
                    $(this).closest('.form-group').remove();
                    actorIndex--;
                });

                // Actor file input change event
                $(document).on('change', '.actorFileInput', function() {
                    const index = $(this).closest('.actor_raw').index();
                    console.log(index);
                    const fileInput = this;
                    const file = fileInput.files[0];

                    // Read the selected file using FileReader
                    const reader = new FileReader();
                    reader.onload = function() {
                        const photoDataUrl = reader.result;
                        console.log($(`#actorsContainer .actor_raw:eq(${index}) .actorPhoto`).attr('src',
                            photoDataUrl).show());
                    };
                    reader.readAsDataURL(file);
                });

                /* s:invoices */
                // Add invoices
                $("#addInvoiceBtn").on('click', function() {
                    const invoiceTemplate = `
                <div class="form-group row invoice_raw">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="invoices[${invoiceIndex}][name]" placeholder="{{ trans('admin.name') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="number" min="1" class="form-control" name="invoices[${invoiceIndex}][cost]" placeholder="{{ trans('admin.cost') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="file" accept=".jpeg, .jpg, .png" name="invoices[${invoiceIndex}][file]" class="form-control invoiceFileInput">
                    </div>
                    <div class="col-1">
                        <img src="#" alt="Invoice Photo" class="rounded-circle w-100 h-50 invoicePhoto" style="display: none;">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger removeInvoiceBtn">{{ trans('admin.delete') }}</button>
                    </div>
                </div>
            `;

                    $("#invoicesContainer").append(invoiceTemplate);
                    invoiceIndex++;
                });

                // Remove invoice
                $(document).on('click', '.removeInvoiceBtn', function() {
                    $(this).closest('.invoice_raw').remove();
                    invoiceIndex--;
                });

                // Actor file input change event
                $(document).on('change', '.invoiceFileInput', function() {
                    const index = $(this).closest('.invoice_raw').index();
                    console.log(index);
                    const fileInput = this;
                    const file = fileInput.files[0];

                    // Read the selected file using FileReader
                    const reader = new FileReader();
                    reader.onload = function() {
                        const photoDataUrl = reader.result;
                        console.log($(`#invoicesContainer .invoice_raw:eq(${index}) .invoicePhoto`).attr(
                            'src', photoDataUrl).show());
                    };
                    reader.readAsDataURL(file);
                });
                /* e:invoices */
                /* s:countries&cites */
                $('#country, #city').select2();

                // Attach change event listener to the "country" select
                $('#country').on('change', function() {
                    var selectedCountryId = $(this).val();

                    // Make an AJAX request to get cities for the selected country
                    $.ajax({
                        url: '{!! route('getCity') !!}?country_id=' + selectedCountryId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            // Clear previous cities and add new ones
                            $('#city').empty();
                            $.each(response, function(index, city) {
                                $('#city').append('<option value="' + city.id + '">' + city
                                    .name_ar + '</option>');
                            });

                            // Refresh the Select2 plugin to update the "city" select
                            $('#city').select2();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error occurred while fetching cities:', error);
                        }
                    });
                });

                /* e:countries&cites */

            });

            function getAllContent() {
                var summernoteContent = $('.editable').summernote('code');
                return summernoteContent;
            }
        </script>
    @endpush
    ``
}