<div class="col">
    <div class="card card-body">
        <h3>{{ $title }}</h3>
        <table class="table table-striped table-hover table-bordered">
            <thead>
            <tr>
                <th scope="col">排名</th>
                <th scope="col">名字</th>
                <th scope="col">国籍</th>
                <th scope="col">生日</th>
                <th scope="col">TOURNAMENTS</th>
                <th scope="col">POINTS</th>
                <th scope="col">比赛记录</th>
            </tr>
            </thead>
            <tbody class="table-group-divider">
            @foreach ($rankings as $ranking)
                <tr>
                    <th scope="row">{{ $ranking->rank }}</th>
                    <td>
                        @empty($ranking->player1->id)
                        @else
                            {{ $ranking->player1->name }}
                            @if($ranking->player2_id > 0)
                                <br/>{{ $ranking->player2->name }}
                            @endempty
                        @endempty
                    </td>
                    <td>
                        @empty($ranking->player1->id)
                        @else
                            <img src="{{ $ranking->player1->withCountry->img_cos_url }}" style="width: 40px; border: 0px solid steelblue" />
                            {{ $ranking->player1->withCountry->name }}

                        @endempty
                    </td>
                    <td>
                        @empty($ranking->player1->id)
                        @else
                            {{ $ranking->player1->date_of_birth }}
                            @if($ranking->player2)
                                <br/>{{ $ranking->player2->date_of_birth }}
                            @endif
                        @endempty
                    </td>
                    <td>{{ $ranking->tournaments }}</td>
                    <td>{{ $ranking->points }}</td>
                    <td><button type="button" class="btn btn-secondary" onclick="showBreakdownModal({{$ranking->id}})">查看</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

