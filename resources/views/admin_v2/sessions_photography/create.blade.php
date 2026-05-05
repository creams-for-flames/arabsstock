@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--tabs">
            <div class="kt-portlet__body">
                <div id="app">
                    <div class="container">
                        <div class="errors">

                        </div>
                        <form id="sessiion" method="post" action="">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ trans('admin.name') }}:</label>
                                    <input type="text" id="folder" name="folder" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>{{ trans('admin.created_session') }}:</label>
                                    <input type="text" id="created_at_picker" name="session_date" class="form-control">
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <label>{{ trans('admin.photographers') }}:</label>
                                    <button type="button" id="addPhotographerBtn" class="btn btn-primary m-2">{{ trans('admin.add') }}
                                        {{ trans('admin.photographers') }}
                                    </button>
                                    <div id="photographersContainer"></div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <label>{{ trans('admin.actors') }}:</label>
                                    <button type="button" id="addActorBtn" class="btn btn-primary m-2">{{ trans('admin.add') }}
                                        {{ trans('admin.actors') }}
                                    </button>
                                    <div id="actorsContainer"></div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>{{ trans('admin.country') }}:</label>
                                    <select name="country_id" id="country" class="form-control">
                                        @foreach ($countries as $country)
                                            <option @if ($country->iso_code_2 === 'SA') selected @endif value="{{ $country->id }}">
                                                {{ $country->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>{{ trans('admin.city') }}:</label>
                                    <select name="city_id" id="city" class="form-control">
                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}">{{ $city->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <div id="locationContainer">
                                <div class="row location">
                                    <input type="hidden" id="location_id" name="location_id" class="form-control">

                                    <div class="col-12 mt-4 mb-4">{{ __('admin.location') }}</div>
                                    <div class="col-md-4">
                                        <label>{{ trans('admin.name') }}:</label>
                                        <select id="location_name" name="location_name" class="form-control">
                                        </select>
                                    </div>
                                    <div class="col-md-4 name">
                                        <label>{{ trans('admin.location_admin') }}:</label>
                                        <input type="text" id="location_admin" name="location_admin" class="form-control">
                                    </div>
                                    <div class="col-md-4 email">
                                        <label>{{ trans('admin.email') }}:</label>
                                        <input type="text" id="location_email" name="location_email" class="form-control">
                                    </div>
                                    <div class="col-md-3 mobile">
                                        <label>{{ trans('admin.mobile') }}:</label>
                                        <input type="text" id="location_mobile" name="location_mobile" class="form-control">
                                    </div>
                                    <div class="col-md-3 license_code">
                                        <label>{{ trans('admin.location_license') }}:</label>
                                        <input type="text" id="license_code" name="license_code" class="form-control">
                                    </div>
                                    <div class="col-md-3 location">
                                        <label>{{ trans('admin.location') }}:</label>
                                        <input type="text" id="location" name="location" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="d-block">{{ trans('admin.contract') }}:</label>
                                        <button type="button" class="btn btn-primary w-100 contractBtn" data-parent_elm="location" data-toggle="modal">{{ __('admin.contract') }}</button>
                                        <textarea class="d-none" name="contract"></textarea>
                                    </div>
                                </div>
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

                                    <div id="invoicesContainer"></div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-12">
                                    <label>{{ trans('admin.notes') }}:</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-5 w-100">{{ __('views.Save') }}</button>
                        </form>
                    </div>

                    <div class="modal fade bd-example-modal-lg" id="contract" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="form-group w-100">
                                    <label class="col-sm-2 control-label">{{ trans('admin.content_ar') }}</label>
                                    <div class="col-sm-12 border p-2">
                                        <textarea name="content_ar" id="content_ar" class="editable" placeholder="{{ trans('admin.content_ar') }}"></textarea>
                                    </div>
                                </div>
                                <div class="form-group p-5 w-100">
                                    <label class="col-sm-12 control-label">{{ trans('admin.add_signature') }}</label>
                                    <div class="col-12">
                                        <canvas id="signatureCanvas" width="400" height="200" class="border"></canvas>
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-danger" id="clearButton">
                                            Clear Signature
                                        </button>
                                        <button type="button" class="btn btn-info" id="displayButton">
                                            Display Signature
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.3/dist/flatpickr.min.css" rel="stylesheet">
        <style>
            .modal-content .note-editor, .modal-content .note-toolbar {
                width: 100%;
            }
            .is-invalid ~ .select2-container .select2-selection {
                border: 1px solid #fd397a !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.3/dist/flatpickr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

        <script>
            var photographerIndex = 0;
            var actorIndex = 0;
            var invoiceIndex = 0;
            var editorWidth, editorHeight;
            var contract_content = {!! json_encode($contract->content_ar??"محرر عقود اكتب نص العقد هنا ...") !!};

            var KTSummernoteDemo = function() {
                var demos = function() {
                    $('.editable').summernote({
                        dialogsInBody: true,
                        airMode: true,
                        callbacks: {
                            onInit: function() {
                                editorWidth = $('#summernote').width();
                                editorHeight = $('#summernote').height();
                            }
                        }
                    });
                }
                return {
                    init: function() {
                        demos();
                    }
                };
            }();

            $(document).ready(function() {
                $('#sessiion').validate({
                    submitHandler: function(form) {
                        const allContractParents = [...$('.photographer_raw'), ...$('.actors_raw'), $('.row.location')[0]];
                        let contractsValidationPassed = true;
                        for (let i = 0; i < allContractParents.length; ++i) {
                            if (!$(allContractParents[i]).find('textarea').val()) {
                                contractsValidationPassed = false;
                                swal.fire("", "Please add contract for all items.", "error");
                                break;
                            }
                        }
                        if (!contractsValidationPassed) return;

                        event.preventDefault();
                        var formData = new FormData(form); // Create a FormData object to send file and other form data

                        $.ajax({
                            type: 'POST',
                            url: "{{ route('admin.sessions.store', $type) }}",
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
                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                                var errs = "";
                                for (const key in errors) {
                                    var err = `
                                        <div class="alert alert-solid-danger alert-bold fade show " role="alert">
                                            <div class="alert-icon">
                                                <i class="fa fa-exclamation-triangle"></i>
                                            </div>
                                            <div class="alert-text">{{ __('views.Oops, something went wrong! Please check the errors below.') }}
                                                ${key}: ${errors[key][0]}
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
                    },
                    rules: {
                        folder: { required: true },
                        session_date: { required: true },
                        country_id: { required: true },
                        city_id: { required: true },
                        location_name: { required: true },
                        location_admin: { required: true },
                        location_email: { required: true },
                        location_mobile: { required: true },
                        license_code: { required: true },
                        location: { required: true },
                    },
                    messages: {
                        folder: { required: "Please enter a folder name." } ,
                        session_date: { required: "Please enter a session date." },
                        country_id: { required: "Please select a country." },
                        city_id: { required: "Please select a city." },
                        location_name: { required: "Please enter a location name." },
                        location_admin: { required: "Please enter a location admin." },
                        location_email: { required: "Please enter a location email." },
                        location_mobile: { required: "Please enter a location mobile." },
                        license_code: { required: "Please enter a license code." },
                        location: { required: "Please enter a location." },
                    },
                    errorElement: "span",
                    errorPlacement: function (error, element) {
                        error.addClass("invalid-feedback");
                        [...element].forEach((node, index) => {
                            if (node.nodeName === "SELECT") error.insertAfter(element.next("span"));
                            else error.insertAfter(element);
                        })
                    },
                    highlight: function (element, errorClass, validClass) {
                        $(element).closest(".form-control").addClass("is-invalid");
                    },
                    unhighlight: function (element, errorClass, validClass) {
                        $(element).closest(".form-control").removeClass("is-invalid");
                    }
                })

                var canvas = document.getElementById('signatureCanvas');
                var ctx = canvas.getContext('2d');
                var isDrawing = false;
                var lastX = 0;
                var lastY = 0;
                var signatureColor = '#0158ff';

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
                    var customHTML =
                    `<div class="custom-box">
                        <table class="table"style="border:unset;">
                            <tbody>
                                <tr>
                                    <td style="position: relative;border-top:unset;"><img src="${dataURL}" /></td>
                                    <td style="position: relative;border-top:unset;"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>`;
                    var newContent = currentContent + customHTML;

                    // Set the new content in the editor
                    $('.editable').summernote('code', newContent);
                    var active_contract = $('.modal-content').data('active_contract');
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

                    $('.modal-content').data('active_contract', elm);
                    $('.bd-example-modal-lg').modal('show');
                    var current_contract = $(`${elm} textarea`).val() ? $(`${elm} textarea`).val() : contract_content;
                    console.log(current_contract);
                    $('.editable').summernote('code', current_contract);
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
                            <div class="col-md-4">
                                <select placeholder="{{ trans('admin.id_number') }}" name="photographers[${photographerIndex}][id_number]" class="form-control photographers-select">
                                </select>
                            </div>
                            <div class="col-md-3 name">
                                <input type="text" class="form-control" name="photographers[${photographerIndex}][name]" placeholder="{{ trans('admin.name') }}">
                            </div>
                            <div class="col-md-3 email">
                                <input type="text" class="form-control" name="photographers[${photographerIndex}][email]" placeholder="{{ trans('admin.email') }}">
                            </div>

                            <div class="d-none">
                                <textarea name="photographers[${photographerIndex}][contract]"></textarea>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary contractBtn" data-parent_elm="photographer_raw" data-toggle="modal">{{ __('admin.contract') }}</button>
                                <button type="button" class="btn btn-danger removePhotographerBtn">{{ trans('admin.delete') }}</button>
                            </div>
                        </div>
                    `;

                    $("#photographersContainer").append(photographerTemplate);
                    [...$(".photographer_raw")].forEach((item, index) => {
                        $('#sessiion').validate().settings.rules[`photographers[${index}][name]`] = {required: true};
                        $('#sessiion').validate().settings.rules[`photographers[${index}][id_number]`] = {required: true};
                        $('#sessiion').validate().settings.rules[`photographers[${index}][email]`] = {required: true};

                        $('#sessiion').validate().settings.messages[`photographers[${index}][name]`] = {required: "Please enter a name."};
                        $('#sessiion').validate().settings.messages[`photographers[${index}][id_number]`] = {required: "Please enter a id number."};
                        $('#sessiion').validate().settings.messages[`photographers[${index}][email]`] = {required: "Please enter a email."};
                    });

                    const selectOptions = $('.photographers-select');
                    selectOptions.select2({
                        tags: true,
                        ajax: {
                            url: "{{route('admin.sessions.photographers',['type'=>$type])}}",
                            data: (params) => ({ keyword: params.term }),
                            processResults: (data) => {
                                const currentSelectedItems = [...$('.photographers-select')].map(item => {
                                    return $(item).val();
                                });
                                return {
                                    results: data[0].filter((item) => {
                                        return !currentSelectedItems.includes(item.id_number);
                                    }).map((item) => {
                                        return {
                                            ...item,
                                            id: item.id_number,
                                            text: item.id_number,
                                        };
                                    })
                                };
                            }
                        },
                    });
                    selectOptions.on('select2:select', function (e) {
                        const matchingSelection = e.params.data;
                        const parentRow = $(this).parents('.photographer_raw');
                        parentRow.children('.name').children("input").val(matchingSelection.name);
                        parentRow.children('.email').children("input").val(matchingSelection.email);
                    });
                    selectOptions.on('select2:clear', function (e) {
                        selectOptions.val(null).trigger("change");
                        const parentRow = $(this).parents('.photographer_raw');
                        parentRow.children('name').children("input").val("");
                        parentRow.children('.email').children("input").val("");
                    });
                    selectOptions.on('select2:close', function (e) {
                        const currentSelectedItems = [...$('.photographers-select')].map(item => {
                            return $(item).val();
                        });
                        if (currentSelectedItems.filter(x => x === e.target.value).length > 1) {
                            $(this).val(null).trigger("change");
                            swal.fire("", "This photographer is already selected.", "error");
                        }
                    });

                    photographerIndex++;
                });

                const locationsSelect = $('#location_name');
                locationsSelect.select2({
                    tags: true,
                    ajax: {
                        url: "{{route('admin.sessions.locations',['type'=>$type])}}",
                        data: (params) => ({ keyword: params.term }),
                        processResults: (data) => {
                            return {
                                results: data[0].map((item) => {
                                    return {
                                        ...item,
                                        id: item.id,
                                        text: item.name,
                                    };
                                }),
                            };
                        }
                    },
                });
                locationsSelect.on('select2:select', function (e) {
                    const matchingSelection = e.params.data;
                    const parentRow = $(this).parents('.location');
                    const inputs = ['name', 'email', 'mobile', 'license_code', 'location'];
                    inputs.forEach((input) => {
                        parentRow.children(`.${input}`).children("input").val(matchingSelection[input]);
                    });
                });
                locationsSelect.on('select2:clear', function (e) {
                    locationsSelect.val(null).trigger("change");
                    const parentRow = $(this).parents('.location');
                    const inputs = ['name', 'email', 'mobile', 'license_code', 'location'];
                    inputs.forEach((input) => {
                        parentRow.children(`.${input}`).children("input").val("");
                    });
                });

                // Remove Photographer
                $(document).on('click', '.removePhotographerBtn', function() {
                    $(this).closest('.photographer_raw').remove();
                    photographerIndex--; // Decrement the index when removing
                });

                // Add Actor
                $('#addActorBtn').on('click', function() {
                    const actorTemplate = `
                        <div class="form-group row actors_raw">
                            <div class="col-md-2">
                                <select placeholder="{{ trans('admin.id_number') }}" name="actors[${actorIndex}][id_number]" class="form-control actors-select">
                                </select>
                            </div>
                            <div class="col-md-3 name">
                                <input type="text" class="form-control" name="actors[${actorIndex}][name]" placeholder="{{ trans('admin.name') }}">
                            </div>
                            <div class="col-md-3 email">
                                <input type="text" class="form-control" name="actors[${actorIndex}][email]" placeholder="{{ trans('admin.email') }}">
                            </div>
                            <div class="col-md-2 file">
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
                    [...$(".actors_raw")].forEach((item, index) => {
                        console.log(index, item);
                        $('#sessiion').validate().settings.rules[`actors[${index}][name]`] = {required: true};
                        $('#sessiion').validate().settings.rules[`actors[${index}][id_number]`] = {required: true};
                        $('#sessiion').validate().settings.rules[`actors[${index}][email]`] = {required: true};

                        $('#sessiion').validate().settings.messages[`actors[${index}][name]`] = {required: "Please enter a name."};
                        $('#sessiion').validate().settings.messages[`actors[${index}][id_number]`] = {required: "Please enter a id number."};
                        $('#sessiion').validate().settings.messages[`actors[${index}][email]`] = {required: "Please enter a email."};
                    });


                    const selectOptions = $('.actors-select');
                    selectOptions.select2({
                        tags: true,
                        ajax: {
                            url: "{{route('admin.sessions.actors',['type'=>$type])}}",
                            data: (params) => ({ keyword: params.term }),
                            processResults: (data) => {
                                const currentSelectedItems = [...$('.actors-select')].map(item => {
                                    return $(item).val();
                                });
                                return {
                                    results: data[0].filter((item) => {
                                        return !currentSelectedItems.includes(item.id_number);
                                    }).map((item) => {
                                        return {
                                            ...item,
                                            id: item.id_number,
                                            text: item.id_number,
                                        };
                                    })
                                };
                            }
                        },
                    });
                    selectOptions.on('select2:select', function (e) {
                        const matchingSelection = e.params.data;
                        const parentRow = $(this).parents('.actors_raw');
                        parentRow.children('.name').children("input").val(matchingSelection.name);
                        parentRow.children('.email').children("input").val(matchingSelection.email);
                    });
                    selectOptions.on('select2:clear', function (e) {
                        selectOptions.val(null).trigger("change");
                        const parentRow = $(this).parents('.actors_raw');
                        parentRow.children('.name').children("input").val("");
                        parentRow.children('.email').children("input").val("");
                    });
                    selectOptions.on('select2:close', function (e) {
                        const currentSelectedItems = [...$('.actors-select')].map(item => {
                            return $(item).val();
                        });
                        if (currentSelectedItems.filter(x => x === e.target.value).length > 1) {
                            $(this).val(null).trigger("change");
                            swal.fire("", "This actor is already selected.", "error");
                        }
                    });
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
                    const fileInput = this;
                    const file = fileInput.files[0];

                    // Read the selected file using FileReader
                    const reader = new FileReader();
                    reader.onload = function() {
                        const photoDataUrl = reader.result;
                    };
                    reader.readAsDataURL(file);
                });

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
                    [...$(".invoice_raw")].forEach((item, index) => {
                        $('#sessiion').validate().settings.rules[`invoices[${index}][name]`] = {required: true};
                        $('#sessiion').validate().settings.rules[`invoices[${index}][cost]`] = {required: true};
                        $('#sessiion').validate().settings.rules[`invoices[${index}][file]`] = {required: true};

                        $('#sessiion').validate().settings.messages[`invoices[${index}][name]`] = {required: "Please enter a name."};
                        $('#sessiion').validate().settings.messages[`invoices[${index}][cost]`] = {required: "Please enter cost."};
                        $('#sessiion').validate().settings.messages[`invoices[${index}][file]`] = {required: "Please upload file"};
                    });

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
                    const fileInput = this;
                    const file = fileInput.files[0];

                    // Read the selected file using FileReader
                    const reader = new FileReader();
                    reader.onload = function() {
                        const photoDataUrl = reader.result;
                    };
                    reader.readAsDataURL(file);
                });
                $('#country, #city').select2();
                $('#country').on('change', function() {
                    var selectedCountryId = $(this).val();
                    $.ajax({
                        url: '{!! route('getCity') !!}?country_id=' + selectedCountryId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            $('#city').empty();
                            $.each(response, function(index, city) {
                                $('#city').append('<option value="' + city.id + '">' + city.name_ar + '</option>');
                            });
                            $('#city').select2();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error occurred while fetching cities:', error);
                        }
                    });
                });
            });
            function getAllContent() {
                var summernoteContent = $('.editable').summernote('code');
                return summernoteContent;
            }
        </script>
    @endpush
    ``
