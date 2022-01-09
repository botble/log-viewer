@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @include('plugins/log-viewer::partials.style')
    <div class="main-form">
    <div class="row">
        <div class="col-md-2">
            @include('plugins/log-viewer::partials.menu')
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <div class="panel-heading" style="height: 48px;line-height: 30px;">
                    {{ trans('plugins/log-viewer::log-viewer.log_info') }} :
                    <div class="group-btns float-end">
                        <a href="{{ route('log-viewer::logs.download', [$log->date]) }}" class="btn btn-success">
                            <i class="fa fa-download"></i> {{ trans('plugins/log-viewer::log-viewer.download') }}
                        </a>
                        <a href="#delete-log-modal" class="btn btn-danger" data-bs-toggle="modal">
                            <i class="fa fa-trash"></i> {{ trans('plugins/log-viewer::log-viewer.delete') }}
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <td>{{ trans('plugins/log-viewer::log-viewer.file_path') }} :</td>
                            <td colspan="5"><strong>{{ $log->getPath() }}</strong></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ trans('plugins/log-viewer::log-viewer.log_entries') }} :</td>
                            <td>
                                <span class="label label-primary">{{ $entries->total() }}</span>
                            </td>
                            <td>{{ trans('plugins/log-viewer::log-viewer.size') }} :</td>
                            <td>
                                <span class="label label-primary">{{ $log->size() }}</span>
                            </td>
                            <td>{{ trans('core/base::tables.created_at') }} :</td>
                            <td>
                                <span class="label label-primary">{{ $log->createdAt() }}</span>
                            </td>
                            <td>{{ trans('core/base::tables.updated_at') }} :</td>
                            <td>
                                <span class="label label-primary">{{ $log->updatedAt() }}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel panel-default">
                @if ($entries->hasPages())
                    <div class="panel-heading">
                        {!! $entries->render() !!}
                        <span class="label label-info float-end">
                            {{ trans('plugins/log-viewer::log-viewer.page') }} {!! $entries->currentPage() !!} {{ trans('plugins/log-viewer::log-viewer.of') }} {!! $entries->lastPage() !!}
                        </span>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="entries" class="table table-condensed">
                        <thead>
                        <tr>
                            <th width="100">{{ trans('plugins/log-viewer::log-viewer.env') }}</th>
                            <th width="120">{{ trans('plugins/log-viewer::log-viewer.level') }}</th>
                            <th width="65">{{ trans('plugins/log-viewer::log-viewer.time') }}</th>
                            <th>{{ trans('plugins/log-viewer::log-viewer.header') }}</th>
                            <th class="text-center" width="100">{{ trans('plugins/log-viewer::log-viewer.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($entries as $key => $entry)
                            <tr>
                                <td>
                                    <span class="label label-env">{{ $entry->env }}</span>
                                </td>
                                <td>
                                    <span class="level level-{{ $entry->level }}">{!! $entry->level() !!}</span>
                                </td>
                                <td>
                                    <span class="label label-default">{{ $entry->datetime->format('H:i:s') }}</span>
                                </td>
                                <td>
                                    <p>{{ $entry->header }}</p>
                                </td>
                                <td class="text-right">
                                    @if ($entry->hasStack())
                                        <a class="btn btn-secondary" role="button" data-bs-toggle="collapse"
                                           href="#log-stack-{{ $key }}" aria-expanded="false"
                                           aria-controls="log-stack-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i> {{ trans('plugins/log-viewer::log-viewer.stack') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @if ($entry->hasStack())
                                <tr>
                                    <td colspan="5" class="stack">
                                        <div class="stack-content collapse" id="log-stack-{{ $key }}">
                                            {!! preg_replace("/\n/", '<br>', $entry->stack) !!}
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($entries->hasPages())
                    <div class="panel-footer">
                        {!! $entries->render() !!}
                        <span class="label label-info float-end">
                            Page {!! $entries->currentPage() !!} of {!! $entries->lastPage() !!}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="delete-log-modal" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-log-form" action="{{ route('log-viewer::logs.destroy') }}" method="post">
                @method('DELETE')
                @csrf
                <input type="hidden" name="date" value="{{ $log->date }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><i class="til_img"></i><strong>{{ trans('plugins/log-viewer::log-viewer.delete_log_file') }}</strong></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{!! trans('plugins/log-viewer::log-viewer.confirm_delete_msg', ['date' => $log->date]) !!}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary float-start" data-bs-dismiss="modal">{{ trans('core/base::forms.cancel') }}</button>
                        <button type="submit" class="btn btn-sm btn-danger" id="delete-log-button">{{ trans('plugins/log-viewer::log-viewer.delete_button') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('javascript')
    <script>
        'use strict';
        $(function () {
            var deleteLogModal = $('div#delete-log-modal');
            var deleteLogForm = $('form#delete-log-form');
            var submitBtn = deleteLogForm.find('button[type=submit]');

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
                            location.replace('{{ route('log-viewer::logs.index') }}');
                        } else {
                            Botble.showError('OOPS ! This is a lack of coffee exception !');
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
        });
    </script>
@endsection
