@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @include('plugins/log-viewer::partials.style')
    <div class="main-form">

    {!! $rows->render() !!}

    <div class="table-responsive">
        <table class="table table-striped custom-table m-b-0 table-stats table-log-viewer">
            <thead>
                <tr>
                    @foreach($headers as $key => $header)
                        <th class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                            @if ($key == 'date')
                                <span class="label label-info">{{ $header }}</span>
                            @else
                                <span class="level level-{{ $key }}">
                                    {!! log_styler()->icon($key) . ' ' . $header !!}
                                </span>
                            @endif
                        </th>
                    @endforeach
                    <th class="text-center" width="120">{{ trans('plugins/log-viewer::log-viewer.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @if (count($rows) > 0)
                    @foreach($rows as $date => $item)
                        <tr>
                            @foreach($item as $key => $value)
                                <td class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                                    @if ($key == 'date')
                                        <span class="label label-primary">{{ $value }}</span>
                                    @elseif ($value == 0)
                                        <span class="level level-empty">{{ $value }}</span>
                                    @else
                                        <a href="{{ route('log-viewer::logs.filter', [$date, $key]) }}">
                                            <span class="level level-{{ $key }}">{{ $value }}</span>
                                        </a>
                                    @endif
                                </td>
                            @endforeach
                            <td class="text-right" style="width: 150px;">
                                <a href="{{ route('log-viewer::logs.show', [$date]) }}" class="btn btn-xs btn-info">
                                    <i class="fa fa-search"></i>
                                </a>
                                <a href="{{ route('log-viewer::logs.download', [$date]) }}" class="btn btn-xs btn-success">
                                    <i class="fa fa-download"></i>
                                </a>
                                <a href="#delete-log-modal" class="btn btn-xs btn-danger" data-log-date="{{ $date }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="text-center">{{ trans('plugins/log-viewer::log-viewer.no_error') }}</td>
                        </tr>
                    @endif
            </tbody>
        </table>
    </div>

    {!! $rows->render() !!}

    <div id="delete-log-modal" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-log-form" action="{{ route('log-viewer::logs.destroy') }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="date">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><i class="til_img"></i><strong>{{ trans('plugins/log-viewer::log-viewer.delete_log_file') }}</strong></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>

                    <div class="modal-body with-padding">
                        <p>{!! trans('plugins/log-viewer::log-viewer.confirm_delete_msg', ['date' => null]) !!}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary float-start" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                        <button type="submit" class="btn btn-sm btn-danger">{{ trans('plugins/log-viewer::log-viewer.delete_button') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
@stop

@section('javascript')
    <script>
        'use strict';
        $(function () {
            var deleteLogModal = $('div#delete-log-modal');
            var deleteLogForm = $('form#delete-log-form');
            var submitBtn = deleteLogForm.find('button[type=submit]');

            $('a[href="#delete-log-modal"]').on('click', function (event) {
                event.preventDefault();
                var date = $(this).data('log-date');
                deleteLogForm.find('input[name=date]').val(date);
                deleteLogModal.find('.modal-body p .log_date').text(date);

                deleteLogModal.modal('show');
            });

            deleteLogForm.on('submit', function (event) {
                event.preventDefault();
                submitBtn.addClass('button-loading');

                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function (data) {
                        if (data.result === 'success') {
                            deleteLogModal.modal('hide');
                            location.reload();
                        }
                        else {
                            Botble.showError('AJAX ERROR ! Check the console !');
                            console.error(data);
                        }

                        submitBtn.removeClass('button-loading');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        Botble.showError('AJAX ERROR ! Check the console !');
                        console.error(errorThrown);
                        submitBtn.removeClass('button-loading');
                    }
                });
                return false;
            });

            deleteLogModal.on('hidden.bs.modal', function () {
                deleteLogForm.find('input[name=date]').val('');
                deleteLogModal.find('.modal-body p').html('');
            });
        });
    </script>
@stop
