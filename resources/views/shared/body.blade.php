<tr>
    <th scope="row">{{ $person->rank }}</th>
    <td>
        {{ $person->player1->name }}
        @if($person->player2)
            <br/>
            {{ $person->player1->name }}
        @endif
    </td>
    <td>{{ $person->player1->withCountry->name  }}</td>
    <td>{{ $person->player1->date_of_birth }}<br>{{ $person->player2->date_of_birth }}</td>
    <td>{{ $person->tournaments }}</td>
    <td>{{ $person->points }}</td>
    <td>#</td>
</tr>
