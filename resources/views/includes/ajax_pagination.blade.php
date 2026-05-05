@if( $results->count() != 0 )
    <script>
        function delay(callback, ms) {
            var timer = 0;
            return function () {
                var context = this, args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback.apply(context, args);
                }, ms || 0);
            };
        }

        $(document).on('click', '.search-pagination .page-link[data-page]', function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.closest('.search-pagination').find('.page-item.number-input input').val($this.attr('data-page')).trigger('change');
        });
        $(document).on('click', '.next-page.page-link[data-page]', function (e) {
            e.preventDefault();
            var $this = $(this);
            $('.page-item.number-input input:first').val($this.attr('data-page')).trigger('change');
        });
        $(document).on('change keydown', '.search-pagination .page-item.number-input input', function (e) {
            if (e.type == 'change' || (e.type == 'keydown' && (e.which == 13))) {
                var $page = $(this).val();
                fetchDataPagingAjax($page);
            }
        });

        function fetchDataPagingAjax($page, push_state = true) {
            $url = '{{ route(request()->route()->getName(),request()->route()->parameters()) }}?page=' + $page;
            if ($page > {{ $results->lastPage() }})
                return false;
            if ($page >= {{ $results->lastPage() }}) {
                $('.next-page.page-link').fadeOut()
            } else {
                $('.next-page.page-link').fadeIn()
            }
            if ($page > 0) {
                $.ajax({
                    type: "GET",
                    url: $url,
                    success: function (response) {
                        var $response = $(response);
                        if (push_state) {
                            window.history.pushState({'page': $page}, null, $url);
                        }
                        var dataLastPageValue = Number($('.next-page.page-link').attr('data-lastpage'));
                        var dataPageValue = Number($page);
                        if (dataPageValue >= dataLastPageValue) {
                            $('.next-page.page-link').addClass('d-none');
                        }else{
                            $('.next-page.page-link').removeClass('d-none');

                        }
                        var selector = '{{ $selector }}';
                        $(selector).html('')
                        $(selector).html($response.find(selector).html());
                        if (selector === '#videogrid') {
                         $('#videogrid').flexImages({object: '.arabs-video', rowHeight: 300, truncate: 1});
                        }else{
                            $(selector).flexImages({rowHeight: 300});
                        }
                        $('.search-pagination .pagination').remove();
                        $('.search-pagination').prepend($response.find('.search-pagination .pagination')[0].outerHTML);
                        $('.next-page.page-link').attr('data-page', $('.search-pagination .page-link[rel="next"]').attr('data-page'))
                        $('html,body').animate({
                            scrollTop: 0,
                        }, 0);

                        $(".search-header").focus();
                    },
                    dataType: 'HTML'
                });
            }
        }

        window.addEventListener('popstate', function (event) {
            event.preventDefault();
            var $page = event.state !== null ? event.state.page : 1;
            fetchDataPagingAjax($page, false);
        });
    </script>
@endif
