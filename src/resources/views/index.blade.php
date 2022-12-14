@extends($layout)
@section($header)
@endsection
@section($main)
    <h1>{{$table}}</h1>
    <div class="card card-flush p-4">
        @props(['data', 'route_create', 'route_update', 'route_delete', 'links', 'hidens'])
        @if (isset($config['create']))
            <div class=" d-flex justify-content-end align-content-end">
                <a class="btn btn-primary" href="{{ route($config['create']) }}" role="button">Create</a>
            </div>
        @endif
        <div class="table-responsive table-responsive-md">

            @if (count($data) > 0)
                <table class="table table-row-bordered table-row-gray-300 gy-7  table-hover ">
                    <thead>
                    <tr>
                        <th>#</th>
                        @foreach ($data[0] as $key => $item)
                            @if (!in_array($key, $rolColume))
                                <th>{{ \Str::title($key) }}</td>
                            @endif
                        @endforeach

                        <th style="width: 10%">
                            <span class="svg-icon svg-icon-dark svg-icon-2x">
                                <!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo2/dist/../src/media/svg/icons/General/Settings-2.svg--><svg
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z"
                                            fill="#000000" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        </th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($data as $k => $v)
                        <tr>
                            <td>{{ $k + 1 }}</td>
                            @foreach ($v as $key => $item)
                                @if (!in_array($key, $rolColume))
                                    <td>
                                        @if (in_array($key, $medias))
                                            <img style="max-width: 130px" src="{{ asset('images/' . $item) }}"
                                                 alt="">
                                        @elseif (in_array($key, $dataMuntipleStatus))
                                            @foreach ($dataMuntipleStatusValue[$key] as $k1 => $v1)
                                                @if ($k1 == $item)
                                                    {{ $v1 }}
                                                @endif
                                            @endforeach
                                        @else
                                            @if (is_array($item))
                                                @if (isset($relationshipModel[$key]))
                                                    {{ $item[$relationshipModel[$key]] }}
                                                @else
                                                    {{ $item['name'] ?? 'Ch??a t???n t???i key name ' }}
                                                @endif
                                            @elseif (isset($links[$key]))
                                                @if (isset($item['status']))
                                                    @if ($item['status'] == 1)
                                                        <a
                                                            href="{{ route($links[$key], ['id' => $v['id']]) }}">{{ $item }}</a>
                                                    @else
                                                        {{ $item }}
                                                    @endif
                                                @else
                                                    <a
                                                        href="{{ route($links[$key], ['id' => $v['id']]) }}">{{ $item }}</a>
                                                @endif
                                            @else
                                                {{ $item }}
                                            @endif
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                            <td>
                                <a href="{{ route($config['edit'],['id' => $v['id']]) }}" class="btn btn-primary">Update</a>
                                <form action="{{ route($config['delete'],['id' => $v['id']]) }}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-primary">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                No item
            @endif

            <!-- Simplicity is the consequence of refined emotions. - Jean D'Alembert -->
        </div>
    </div>

@endsection
@section($footer)
@endsection
